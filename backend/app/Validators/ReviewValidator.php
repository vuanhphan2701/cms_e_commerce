<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class ReviewValidator extends BaseValidator
{
    public static function validateCreate(): array
    {
        return [
            'rating'        => 'required|integer|min:1|max:5',
            'content'       => 'nullable|string',
            'images'        => 'nullable|string',   // JSON string
            'is_verified'   => 'nullable|boolean',
            'reply_content' => 'nullable|string',
            'reply_at'      => 'nullable|date',
            'reply_user_id' => 'nullable|integer',
            'like_count'    => 'nullable|integer|min:0',
            'status'        => 'nullable|integer|in:0,1',
            'product_id'    => 'required|integer',
            'user_id'       => 'required|integer',
        ];
    }
    public static function validateUpdate(int $id): array
    {
        return [
            'rating'        => 'required|integer|min:1|max:5',
            'content'       => 'nullable|string',
            'images'        => 'nullable|string',
            'is_verified'   => 'nullable|boolean',
            'reply_content' => 'nullable|string',
            'reply_at'      => 'nullable|date',
            'reply_user_id' => 'nullable|integer',
            'like_count'    => 'nullable|integer|min:0',
            'status'        => 'nullable|integer|in:0,1',
            'product_id'    => 'required|integer',
            'user_id'       => 'required|integer',
            'version'       => 'required|integer|min:1',
        ];
    }
}
