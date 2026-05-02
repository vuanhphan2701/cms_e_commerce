<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'failed_login_attempts',
        'locked_until',
        'email_verification_otp',
        'email_verification_otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'failed_login_attempts',
        'locked_until',
        'email_verification_otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'email_verification_otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if the account is currently locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock the account for a given number of minutes.
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Increment failed login attempts. Lock if threshold is reached.
     */
    public function incrementFailedAttempts(int $maxAttempts = 5, int $lockMinutes = 30): void
    {
        $this->increment('failed_login_attempts');

        if ($this->failed_login_attempts >= $maxAttempts) {
            $this->lockAccount($lockMinutes);
        }
    }

    /**
     * Reset failed login attempts on successful login.
     */
    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Generate a new 6-digit OTP for email verification.
     */
    public function generateEmailOtp(int $expiresInMinutes = 15): string
    {
        // Generate a 6 digit random number
        $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => now()->addMinutes($expiresInMinutes),
        ]);

        return $otp;
    }

    /**
     * Verify the provided OTP.
     */
    public function verifyEmailOtp(string $otp): bool
    {
        if (!$this->email_verification_otp || !$this->email_verification_otp_expires_at) {
            return false;
        }

        if ($this->email_verification_otp !== $otp) {
            return false;
        }

        if ($this->email_verification_otp_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Clear OTP data.
     */
    public function clearEmailOtp(): void
    {
        $this->update([
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ]);
    }

    /**
     * Override: send custom email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    /**
     * Override: send custom password reset notification.
     * Points reset link to the frontend app instead of the backend.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
