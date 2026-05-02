<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use Core\Controllers\BaseController;
use Core\Response;
use Illuminate\Http\Request;
use App\Validators\AuthValidator;

class AuthController extends BaseController
{
    protected AuthService $authService;
    protected string $validator = AuthValidator::class;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $this->validate('validateRegister');

        try {
            $result = $this->authService->register($request->all());
            return Response::success($result, 'Người dùng đã được đăng ký thành công', 201);
        } catch (\Exception $e) {
            return Response::error('Đăng ký thất bại: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $this->validate('validateLogin');

        $result = $this->authService->login($request->only('email', 'password'), $request->ip());

        if (!$result) {
            return Response::error('Thông tin đăng nhập không chính xác.', 401);
        }

        // Handle error responses from AuthService (rate_limit, account_locked, etc.)
        if (isset($result['error']) && $result['error'] === true) {
            $statusCode = match ($result['type'] ?? '') {
                'rate_limit' => 429,
                'account_locked' => 423,
                'email_not_verified' => 403,
                default => 401,
            };
            return Response::error($result['message'], $statusCode, [
                'type' => $result['type'] ?? 'unknown',
                'retry_after' => $result['retry_after'] ?? null,
            ]);
        }

        return Response::success($result, 'Đăng nhập thành công');
    }

    /**
     * Refresh current token.
     */
    public function refresh()
    {
        try {
            $result = $this->authService->refresh();
            return Response::success($result, 'Làm mới token thành công');
        } catch (\Exception $e) {
            return Response::error('Không thể làm mới token: ' . $e->getMessage(), 401);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        $user = $this->authService->me();
        if (!$user) {
            return Response::error('Người dùng không tồn tại hoặc chưa đăng nhập', 404);
        }
        return Response::success($user, 'Lấy thông tin người dùng thành công');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        $this->authService->logout();
        return Response::success(null, 'Đăng xuất thành công');
    }

    /**
     * Verify user email via OTP.
     */
    public function verifyOtp(Request $request)
    {
        $this->validate('validateVerifyOtp');

        $user = \App\Models\User::where('email', $request->input('email'))->first();

        if (!$user) {
            return Response::error('Người dùng không tồn tại.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return Response::success(null, 'Email đã được xác thực trước đó.');
        }

        if (!$user->verifyEmailOtp($request->input('otp'))) {
            return Response::error('Mã xác thực không hợp lệ hoặc đã hết hạn.', 400);
        }

        // OTP is valid
        $user->markEmailAsVerified();
        $user->clearEmailOtp();

        // Automatically log the user in
        $token = auth('api')->login($user);

        return Response::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
        ], 'Xác thực email thành công.');
    }

    /**
     * Resend email verification notification via OTP.
     */
    public function resendVerification(Request $request)
    {
        $this->validate('validateResendVerification');

        $user = \App\Models\User::where('email', $request->input('email'))->first();

        if (!$user) {
            return Response::error('Người dùng không tồn tại.', 404);
        }

        try {
            $result = $this->authService->sendVerificationEmail($user);
            return Response::success(null, $result['message']);
        } catch (\Exception $e) {
            return Response::error('Không thể gửi email xác thực: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $this->validate('validateForgotPassword');

        try {
            $result = $this->authService->sendPasswordResetLink($request->input('email'));
            return Response::success(null, $result['message']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Forgot password error: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);
            return Response::error('Không thể gửi email đặt lại mật khẩu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reset password with token.
     */
    public function resetPassword(Request $request)
    {
        $this->validate('validateResetPassword');

        try {
            $result = $this->authService->resetPassword($request->all());

            if ($result['success']) {
                return Response::success(null, $result['message']);
            }

            return Response::error($result['message'], 400);
        } catch (\Exception $e) {
            return Response::error('Không thể đặt lại mật khẩu: ' . $e->getMessage(), 500);
        }
    }
}
