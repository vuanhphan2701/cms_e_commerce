<?php

namespace App\Repositories;

use App\Models\AdminSession;
use Core\Repositories\BaseRepository;

class AdminSessionRepository extends BaseRepository
{
    protected string $model = AdminSession::class;

    public function findByRefreshToken(string $hashedToken)
    {
        return $this->model::where('refresh_token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteByRefreshToken(string $hashedToken)
    {
        return $this->model::where('refresh_token', $hashedToken)->delete();
    }

    public function deleteAllByAdminId(int $adminId)
    {
        return $this->model::where('admin_id', $adminId)->delete();
    }

    public function getActiveSessionsByAdminId(int $adminId)
    {
        return $this->model::where('admin_id', $adminId)
            ->select('id', 'ip_address', 'user_agent', 'is_trusted', 'last_activity_at', 'created_at')
            ->get();
    }
}
