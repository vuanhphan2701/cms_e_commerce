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
     * Search and filter users for admin panel.
     */
    public function searchAndFilter(array $filters, int $perPage = 15)
    {
        $query = $this->model::query();

        if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (isset($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['rating_min'])) {
            $query->where('rating', '>=', $filters['rating_min']);
        }

        if (isset($filters['rating_max'])) {
            $query->where('rating', '<=', $filters['rating_max']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function incrementFailedAttempts(int $userId, int $maxAttempts = 5, int $lockMinutes = 30)
    {
        $user = $this->find($userId);
        $user->increment('failed_login_attempts');

        if ($user->failed_login_attempts >= $maxAttempts) {
            $this->update($userId, [
                'locked_until' => now()->addMinutes($lockMinutes),
            ]);
        }
    }

    public function resetFailedAttempts(int $userId)
    {
        $this->update($userId, [
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function generateEmailOtp(int $userId, int $expiresInMinutes = 15)
    {
        $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $this->update($userId, [
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => now()->addMinutes($expiresInMinutes),
        ]);

        return $otp;
    }

    public function clearEmailOtp(int $userId)
    {
        $this->update($userId, [
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ]);
    }

    public function markEmailAsVerified(int $userId)
    {
        $this->update($userId, [
            'email_verified_at' => now(),
        ]);
    }
}
