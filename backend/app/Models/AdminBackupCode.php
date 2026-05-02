<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBackupCode extends Model
{
    protected $fillable = [
        'admin_id',
        'code_hash',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
