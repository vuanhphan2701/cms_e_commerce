<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthService
{
    protected UserRepository $userRepository;

    const MAX_ATTEMPTS = 5;
    const LOCK_MINUTES = 30;
    const RATE_LIMIT_PER_MINUTE = 5;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Generate OTP via Repository
        $this->userRepository->generateEmailOtp($user->id);

        // Fire event
        event(new Registered($user));

        return [
            'user' => $user,
            'message' => 'Vui lòng kiểm tra email để nhận mã xác thực (OTP).'
        ];
    }

    public function login(array $credentials, string $ip)
    {
        $throttleKey = $this->throttleKey($credentials['email'], $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_PER_MINUTE)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return [
                'error' => true,
                'type' => 'rate_limit',
                'message' => "Quá nhiều lần thử. Vui lòng đợi {$seconds} giây.",
                'retry_after' => $seconds
            ];
        }

        $user = $this->userRepository->findByEmail($credentials['email']);
        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            return [
                'error' => true,
                'type' => 'invalid_credentials',
                'message' => 'Thông tin đăng nhập không chính xác.'
            ];
        }

        if ($user->isLocked()) {
            $minutesLeft = (int) ceil(now()->diffInSeconds($user->locked_until) / 60);
            return [
                'error' => true,
                'type' => 'account_locked',
                'message' => "Tài khoản đã bị khóa. Vui lòng thử lại sau {$minutesLeft} phút."
            ];
        }

        if (!$token = auth('api')->attempt($credentials)) {
            RateLimiter::hit($throttleKey, 60);
            
            // Mutation via Repository
            $this->userRepository->incrementFailedAttempts($user->id, self::MAX_ATTEMPTS, self::LOCK_MINUTES);

            $user = $user->fresh();
            $attemptsLeft = self::MAX_ATTEMPTS - $user->failed_login_attempts;
            $message = 'Thông tin đăng nhập không chính xác.';

            if ($user->isLocked()) {
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

        if (!$user->hasVerifiedEmail()) {
            auth('api')->logout();
            return [
                'error' => true,
                'type' => 'email_not_verified',
                'message' => 'Vui lòng xác thực email trước khi đăng nhập.'
            ];
        }

        RateLimiter::clear($throttleKey);
        
        // Mutation via Repository
        $this->userRepository->resetFailedAttempts($user->id);

        return $this->respondWithToken($token);
    }

    public function verifyOtp(string $email, string $otp)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new Exception('Người dùng không tồn tại.');
        }

        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Email đã được xác thực trước đó.', 'user' => $user];
        }

        if (!$user->verifyEmailOtp($otp)) {
            throw new Exception('Mã xác thực không hợp lệ hoặc đã hết hạn.');
        }

        // Mutation via Repository
        $this->userRepository->markEmailAsVerified($user->id);
        $this->userRepository->clearEmailOtp($user->id);

        $token = auth('api')->login($user);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user->fresh(),
        ];
    }

    public function sendVerificationEmailByEmail(string $email)
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new Exception('Người dùng không tồn tại.');
        }
        return $this->sendVerificationEmail($user);
    }

    public function sendVerificationEmail($user)
    {
        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Email đã được xác thực.'];
        }

        // Mutation via Repository
        $this->userRepository->generateEmailOtp($user->id);
        
        $user->sendEmailVerificationNotification();
        return ['message' => 'Mã xác thực mới đã được gửi đến email của bạn.'];
    }

    public function sendPasswordResetLink(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được liên kết đặt lại mật khẩu.'];
        }

        \Illuminate\Support\Facades\Password::sendResetLink(['email' => $email]);

        return ['message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được liên kết đặt lại mật khẩu.'];
    }

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
                // Mutation via Repository
                $this->userRepository->update($user->id, [
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);
                $this->userRepository->resetFailedAttempts($user->id);
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return ['success' => true, 'message' => 'Mật khẩu đã được đặt lại thành công.'];
        }

        return ['success' => false, 'message' => 'Không thể đặt lại mật khẩu. Token không hợp lệ hoặc đã hết hạn.'];
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function logout()
    {
        auth('api')->logout();
    }

    public function me()
    {
        return auth('api')->user();
    }

    protected function respondWithToken($token, $user = null)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user ?? auth('api')->user()
        ];
    }

    protected function throttleKey(string $email, string $ip): string
    {
        return Str::transliterate(Str::lower($email) . '|' . $ip);
    }
}
