<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected UserRepository $userRepository;

    /**
     * Maximum failed login attempts before account lock.
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Account lock duration in minutes.
     */
    const LOCK_MINUTES = 30;

    /**
     * Rate limit: max login requests per minute (per IP+email).
     */
    const RATE_LIMIT_PER_MINUTE = 5;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user, fire Registered event for email verification.
     */
    public function register(array $data)
    {
        $user = $this->userRepository->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Fire event so Laravel sends the verification email
        event(new Registered($user));

        return [
            'user' => $user,
            'message' => 'Vui lòng kiểm tra email để xác thực tài khoản.'
        ];
    }

    /**
     * Authenticate user with rate limiting + account lock + email verification check.
     */
    public function login(array $credentials, string $ip)
    {
        $throttleKey = $this->throttleKey($credentials['email'], $ip);

        // --- Rate Limit Check ---
        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_PER_MINUTE)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return [
                'error' => true,
                'type' => 'rate_limit',
                'message' => "Quá nhiều lần thử. Vui lòng đợi {$seconds} giây.",
                'retry_after' => $seconds
            ];
        }

        // --- Find User ---
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            return [
                'error' => true,
                'type' => 'invalid_credentials',
                'message' => 'Thông tin đăng nhập không chính xác.'
            ];
        }

        // --- Account Lock Check ---
        if ($user->isLocked()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until) + 1;
            return [
                'error' => true,
                'type' => 'account_locked',
                'message' => "Tài khoản đã bị khóa. Vui lòng thử lại sau {$minutesLeft} phút."
            ];
        }

        // --- Attempt Login ---
        if (!$token = auth('api')->attempt($credentials)) {
            RateLimiter::hit($throttleKey, 60);
            $user->incrementFailedAttempts(self::MAX_ATTEMPTS, self::LOCK_MINUTES);

            $attemptsLeft = self::MAX_ATTEMPTS - $user->fresh()->failed_login_attempts;
            $message = 'Thông tin đăng nhập không chính xác.';

            if ($user->fresh()->isLocked()) {
                $message = "Tài khoản đã bị khóa do quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau " . self::LOCK_MINUTES . " phút.";
            } elseif ($attemptsLeft <= 2 && $attemptsLeft > 0) {
                $message .= " Còn {$attemptsLeft} lần thử trước khi tài khoản bị khóa.";
            }

            return [
                'error' => true,
                'type' => 'invalid_credentials',
                'message' => $message
            ];
        }

        // --- Email Verification Check ---
        if (!$user->hasVerifiedEmail()) {
            auth('api')->logout();
            return [
                'error' => true,
                'type' => 'email_not_verified',
                'message' => 'Vui lòng xác thực email trước khi đăng nhập.'
            ];
        }

        // --- Success: clear rate limiter + reset failed attempts ---
        RateLimiter::clear($throttleKey);
        $user->resetFailedAttempts();

        return $this->respondWithToken($token);
    }

    /**
     * Send email verification notification.
     */
    public function sendVerificationEmail($user)
    {
        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Email đã được xác thực.'];
        }

        $user->sendEmailVerificationNotification();
        return ['message' => 'Email xác thực đã được gửi lại.'];
    }

    /**
     * Send password reset link.
     */
    public function sendPasswordResetLink(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            // Don't reveal if user exists — security best practice
            return ['message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được liên kết đặt lại mật khẩu.'];
        }

        $status = \Illuminate\Support\Facades\Password::sendResetLink(['email' => $email]);

        return ['message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được liên kết đặt lại mật khẩu.'];
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(array $data)
    {
        $status = \Illuminate\Support\Facades\Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);
                // Unlock account after successful password reset
                $user->resetFailedAttempts();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return ['success' => true, 'message' => 'Mật khẩu đã được đặt lại thành công.'];
        }

        return ['success' => false, 'message' => 'Không thể đặt lại mật khẩu. Token không hợp lệ hoặc đã hết hạn.'];
    }

    /**
     * Refresh the current token.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        auth('api')->logout();
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        return auth('api')->user();
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken($token, $user = null)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user ?? auth('api')->user()
        ];
    }

    /**
     * Generate a throttle key for rate limiting (email + IP).
     */
    protected function throttleKey(string $email, string $ip): string
    {
        return Str::transliterate(Str::lower($email) . '|' . $ip);
    }
}
