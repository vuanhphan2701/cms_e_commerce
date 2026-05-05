<?php

namespace App\Repositories;

use App\Models\Admin;
use Core\Repositories\BaseRepository;

class AdminRepository extends BaseRepository
{
    protected string $model = Admin::class;

    public function findByEmail(string $email)
    {
        return $this->model::where('email', $email)->first();
    }
}
