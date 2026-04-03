<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class CategoryValidator extends BaseValidator
{
    public static function validateCreate(): array
    {
        return [
            'name'          => 'required|string|max:255|unique:categories,name',
            'summary'       => 'nullable|string',
            'description'   => 'nullable|string',
            'alias'         => 'nullable|string|max:255|unique:categories,alias',
            'image'         => 'nullable|string|max:255',
            'parent_id'     => 'nullable|integer',
            'status'        => 'nullable|integer|in:0,1',
            'version'       => 'nullable|integer',
        ];
    }

    public static function validateUpdate( $id): array
    {

        return [
            'name'          => "required|string|max:255|unique:categories,name,{$id}",
            'summary'       => 'nullable|string',
            'description'   => 'nullable|string',
            'alias'         => "nullable|string|max:255|unique:categories,alias,{$id}",
            'image'         => 'nullable|string|max:255',
            'parent_id'     => 'nullable|integer',
            'status'        => 'nullable|integer|in:0,1',
            'version'       => 'required|integer|min:1',
        ];
    }
}
