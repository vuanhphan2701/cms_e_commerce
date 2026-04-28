<?php

namespace App\Repositories;

use App\Models\User;
use Core\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    protected string $model = User::class;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email)
    {
        return $this->model::where('email', $email)->first();
    }

    /**
     * Find user by name.
     */
    public function findByName(string $name)
    {
        return $this->model::where('name', $name)->first();
    }
}
