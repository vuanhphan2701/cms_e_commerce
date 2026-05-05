<?php

namespace App\Repositories;

use App\Models\FailedLoginAttempt;
use Core\Repositories\BaseRepository;

class FailedLoginAttemptRepository extends BaseRepository
{
    protected string $model = FailedLoginAttempt::class;

    public function findByEmailAndIp(string $email, string $ipAddress)
    {
        return $this->model::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->first();
    }

    public function deleteByEmailAndIp(string $email, string $ipAddress)
    {
        return $this->model::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->delete();
    }
}
