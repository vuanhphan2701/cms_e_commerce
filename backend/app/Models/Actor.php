<?php

namespace App\Models;

use Core\Traits\OptimisticLocking;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use OptimisticLocking;

    protected $table = 'actors';
    protected $fillable = [
        'name',
        'version',
    ];
}
