<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Core\Traits\OptimisticLocking;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    use OptimisticLocking;

    protected $fillable = [
        'name',
        'summary',
        'parent_id',
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
     * A category may belong to a parent category.
     * Relationship key: `parent_id`
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * A category can have many child categories.
     * Relationship key: `parent_id`
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * A category can have many products.
     * Relationship key: `category_id`
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
