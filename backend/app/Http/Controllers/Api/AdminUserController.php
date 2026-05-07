<?php

namespace App\Http\Controllers\Api;

use Core\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\AdminUserService;
use Core\Response;

class AdminUserController extends BaseController
{
    protected AdminUserService $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    public function index(Request $request)
    {
        $users = $this->adminUserService->listUsers($request->all());
        return Response::success($users, 'Lấy danh sách người dùng thành công');
    }

    public function show($id)
    {
        try {
            $user = $this->adminUserService->getUserDetail($id);
            return Response::success($user, 'Lấy chi tiết người dùng thành công');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 404);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,banned,suspended',
            'reason' => 'nullable|string',
            'days' => 'nullable|integer|min:1'
        ]);

        $admin = auth('admin')->user();

        // Only super_admin can BAN permanently
        if ($request->status === 'banned' && $admin->role !== 'super_admin') {
            return Response::error('Chỉ Super Admin mới có quyền khóa vĩnh viễn tài khoản.', 403);
        }

        try {
            $user = $this->adminUserService->updateStatus($id, $request->status, $request->reason, $request->days);
            return Response::success($user, 'Cập nhật trạng thái thành công');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function verifyKYC(Request $request, $id)
    {
        $request->validate([
            'approve' => 'required|boolean',
            'reason' => 'nullable|string'
        ]);

        try {
            $user = $this->adminUserService->verifyKYC($id, $request->approve, $request->reason);
            return Response::success($user, 'Xử lý KYC thành công');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function resetPassword(Request $request, $id)
    {
        try {
            $this->adminUserService->resetPassword($id);
            return Response::success(null, 'Đã gửi liên kết đặt lại mật khẩu cho người dùng.');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function getActivityLogs($id)
    {
        try {
            $logs = $this->adminUserService->getUserActivityLogs($id);
            return Response::success($logs, 'Lấy nhật ký hoạt động thành công');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

}
