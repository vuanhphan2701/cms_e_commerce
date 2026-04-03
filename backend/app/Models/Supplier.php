<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'summary',
        'phone',
        'email',
        'address',
        'image',
        'description',
        'alias',
        'status',
        'version',
        'created_user_id',
        'updated_user_id',
    ];

    //  Relationships
    /**
     * A supplier can have many products.
     * Relationship key: `supplier_id`
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
