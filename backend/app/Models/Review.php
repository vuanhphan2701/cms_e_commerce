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

    protected $casts = [
        'images' => 'array',
        'is_verified' => 'boolean',
        'status' => 'boolean',
        'rating' => 'integer',
        'reply_at' => 'datetime',
    ];

    //  Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyUser()
    {
        return $this->belongsTo(User::class, 'reply_user_id');
    }
}
