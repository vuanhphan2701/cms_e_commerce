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
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'status' => $this->status
        ];
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'city',
        'province',
        'rating',
        'is_verified',
        'identity_card_number',
        'identity_card_front',
        'identity_card_back',
        'zalo_id',
        'rejection_reason',
        'banned_at',
        'suspended_until',
        'failed_login_attempts',
        'locked_until',
        'email_verification_otp',
        'email_verification_otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
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
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'banned_at' => 'datetime',
            'suspended_until' => 'datetime',
            'email_verification_otp_expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'rating' => 'float',
        ];
    }

    /**
     * Check if the account is currently locked due to failed logins.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Verify the provided OTP. (Pure logic check)
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
     * Override: send custom email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    /**
     * Override: send custom password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
