<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'content',
        'images',
        'is_verified',
        'reply_content',
        'reply_at',
        'reply_user_id',
        'like_count',
        'status',
        'version',
        'created_user_id',
        'updated_user_id',
    ];

    //  Relationships
    /**
     * Each review belongs to a product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Each review is written by a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A review may have a reply from another user (e.g. admin/staff).
     */
    public function replyUser()
    {
        return $this->belongsTo(User::class, 'reply_user_id');
    }
}
