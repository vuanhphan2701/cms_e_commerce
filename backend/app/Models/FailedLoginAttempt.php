<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'attempts',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];
}
