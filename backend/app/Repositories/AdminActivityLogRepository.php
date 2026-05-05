<?php

namespace App\Repositories;

use App\Models\AdminActivityLog;
use Core\Repositories\BaseRepository;

class AdminActivityLogRepository extends BaseRepository
{
    protected string $model = AdminActivityLog::class;
}
