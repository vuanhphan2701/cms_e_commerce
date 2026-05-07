<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\AdminActivityLogRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserService
{
    protected UserRepository $userRepository;
    protected AdminActivityLogRepository $adminActivityLogRepository;
    protected NotificationRepository $notificationRepository;

    public function __construct(
        UserRepository $userRepository,
        AdminActivityLogRepository $adminActivityLogRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->adminActivityLogRepository = $adminActivityLogRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function listUsers(array $filters)
    {
        return $this->userRepository->searchAndFilter($filters, $filters['per_page'] ?? 15);
    }

    public function getUserDetail(int $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new \Exception('Người dùng không tồn tại.');
        }

        return $user;
    }

    public function getUserActivityLogs(int $userId)
    {
        return $this->adminActivityLogRepository->getByTarget('user', $userId);
    }

    public function updateStatus(int $userId, string $status, ?string $reason = null, ?int $days = null)
    {
        $user = $this->userRepository->find($userId);
        if (!$user) throw new \Exception('Người dùng không tồn tại.');

        $updateData = ['status' => $status];

        if ($status === 'banned') {
            $updateData['banned_at'] = now();
        } elseif ($status === 'suspended' && $days) {
            $updateData['suspended_until'] = now()->addDays($days);
        } elseif ($status === 'active') {
            $updateData['banned_at'] = null;
            $updateData['suspended_until'] = null;
        }

        if ($reason) {
            $updateData['rejection_reason'] = $reason;
        }

        $this->userRepository->update($userId, $updateData);

        // Notify User
        $this->notificationRepository->save([
            'user_id' => $userId,
            'title' => 'Cập nhật trạng thái tài khoản',
            'content' => "Tài khoản của bạn đã được chuyển sang trạng thái: " . strtoupper($status) . ($reason ? ". Lý do: $reason" : ""),
            'type' => 'account',
        ]);

        $this->logActivity("Changed user {$userId} status to {$status}", 'medium', 'user', $userId);

        return $this->userRepository->find($userId);
    }

    public function verifyKYC(int $userId, bool $approve, ?string $reason = null)
    {
        $user = $this->userRepository->find($userId);
        if (!$user) throw new \Exception('Người dùng không tồn tại.');

        $updateData = [
            'is_verified' => $approve,
            'status' => $approve ? 'active' : 'pending_verification'
        ];

        if (!$approve && $reason) {
            $updateData['rejection_reason'] = $reason;
        }

        $this->userRepository->update($userId, $updateData);

        // Notify User
        $this->notificationRepository->save([
            'user_id' => $userId,
            'title' => $approve ? 'Xác thực danh tính thành công' : 'Xác thực danh tính bị từ chối',
            'content' => $approve 
                ? 'Chúc mừng! Hồ sơ CCCD của bạn đã được duyệt.'
                : 'Hồ sơ xác thực danh tính bị từ chối. Lý do: ' . ($reason ?? 'Thông tin không rõ ràng.'),
            'type' => 'kyc',
        ]);

        $action = $approve ? "Approved" : "Rejected";
        $this->logActivity("{$action} KYC for user {$userId}", 'high', 'user', $userId);

        return $this->userRepository->find($userId);
    }

    public function resetPassword(int $userId)
    {
        $user = $this->userRepository->find($userId);
        if (!$user) throw new \Exception('Người dùng không tồn tại.');

        \Illuminate\Support\Facades\Password::broker('users')->sendResetLink(['email' => $user->email]);

        $this->logActivity("Reset password for user {$userId}", 'medium', 'user', $userId);

        return true;
    }

    private function logActivity(string $action, string $severity = 'low', ?string $targetType = null, ?int $targetId = null)
    {
        $admin = auth('admin')->user();
        $this->adminActivityLogRepository->save([
            'admin_id' => $admin->id,
            'action' => $action,
            'action_type' => 'write', // Updated to match enum/allowed values
            'severity' => $severity,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
