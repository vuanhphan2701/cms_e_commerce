<?php

namespace App\Http\Controllers\Api;

use Core\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\AdminAuthService;
use App\Validators\AdminAuthValidator;
use Core\Response;

class AdminAuthController extends BaseController
{
    protected AdminAuthService $adminAuthService;
    protected string $validator = AdminAuthValidator::class;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function login(Request $request)
    {
        $this->validate('validateLogin');

        $result = $this->adminAuthService->login(
            $request->only('email', 'password', 'two_factor_code', 'setup_secret'),
            $request->ip(),
            $request->userAgent()
        );

        if (isset($result['error']) && $result['error']) {
            return Response::error($result['message'], $result['status']);
        }

        if (isset($result['requires_2fa_setup']) || isset($result['requires_2fa'])) {
            return Response::success($result, 'Yêu cầu xác thực 2FA');
        }

        $cookie = $this->createCookie($result['refresh_token']);

        return Response::success([
            'access_token' => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
            'admin' => $result['admin']
        ], 'Đăng nhập thành công')->withCookie($cookie);
    }

    public function refreshToken(Request $request)
    {
        $rawRefreshToken = $request->cookie('admin_refresh_token');

        if (!$rawRefreshToken) {
            return Response::error('Không tìm thấy refresh token', 401);
        }

        $result = $this->adminAuthService->refreshToken($rawRefreshToken);

        if (isset($result['error']) && $result['error']) {
            return Response::error($result['message'], $result['status']);
        }

        $cookie = $this->createCookie($result['refresh_token']);

        return Response::success([
            'access_token' => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
        ], 'Làm mới token thành công')->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $this->adminAuthService->logout($request->cookie('admin_refresh_token'));

        $cookie = cookie()->forget('admin_refresh_token');

        return Response::success(null, 'Đăng xuất thành công')->withCookie($cookie);
    }

    public function logoutAll()
    {
        $this->adminAuthService->logoutAll();

        $cookie = cookie()->forget('admin_refresh_token');

        return Response::success(null, 'Đã đăng xuất khỏi tất cả thiết bị')->withCookie($cookie);
    }

    public function me()
    {
        $admin = $this->adminAuthService->me();

        if (!$admin) {
            return Response::error('Không tìm thấy thông tin người dùng', 404);
        }

        return Response::success($admin, 'Lấy thông tin thành công');
    }

    public function getSessions()
    {
        $sessions = $this->adminAuthService->getSessions();
        return Response::success($sessions, 'Lấy danh sách phiên đăng nhập thành công');
    }

    public function revokeSession($id)
    {
        $this->adminAuthService->revokeSession($id);
        return Response::success(null, 'Đã thu hồi phiên đăng nhập');
    }

    private function createCookie(string $refreshToken)
    {
        return cookie(
            'admin_refresh_token',
            $refreshToken,
            60 * 24 * 7, // 7 days in minutes
            '/',
            null,
            env('APP_ENV') === 'production', // secure
            true, // httpOnly
            false, // raw
            'Strict' // sameSite
        );
    }
}
