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

        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result) {
            return Response::error('Thông tin đăng nhập không chính xác.', 401);
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
}
