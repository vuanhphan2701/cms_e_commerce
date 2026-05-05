<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'last_login_at',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password_hash',
        'two_factor_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }
    
    public function sessions()
    {
        return $this->hasMany(AdminSession::class);
    }
    
    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }
    
    public function backupCodes()
    {
        return $this->hasMany(AdminBackupCode::class);
    }
}
