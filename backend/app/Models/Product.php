<?php

namespace App\Models;

use Core\Traits\OptimisticLocking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    use OptimisticLocking;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'brand_id',
        'sku',
        'name',
        'price',
        'quantity',
        'content',
        'summary',
        'image',
        'images',
        'average_rating',
        'description',
        'alias',
        'status',
        'version',
        'created_user_id',
        'updated_user_id',
    ];

    // Relationships
    /**
     * Each product belongs to one category.
     * Relationship key: `category_id`
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Each product belongs to one supplier.
     * Relationship key: `supplier_id`
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Each product belongs to one brand.
     * Relationship key: `brand_id`
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Each product can have many reviews from users.
     * Relationship key: `product_id`
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
