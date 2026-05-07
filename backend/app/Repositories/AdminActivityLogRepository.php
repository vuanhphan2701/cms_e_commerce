<?php

namespace App\Repositories;

use App\Models\AdminActivityLog;
use Core\Repositories\BaseRepository;

class AdminActivityLogRepository extends BaseRepository
{
    protected string $model = AdminActivityLog::class;

    public function getByTarget(string $targetType, int $targetId)
    {
        return $this->model::where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->with('admin:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

