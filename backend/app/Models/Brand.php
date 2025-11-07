<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'summary',
        'image',
        'description',
        'alias',
        'status',
        'version',
        'created_user_id',
        'updated_user_id',
    ];

    // Relationships
    /**
     * A brand can have many products.
     * Relationship key: `brand_id`
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
