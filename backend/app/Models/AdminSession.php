<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSession extends Model
{
    protected $fillable = [
        'admin_id',
        'refresh_token',
        'ip_address',
        'user_agent',
        'is_trusted',
        'last_activity_at',
        'expires_at',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
