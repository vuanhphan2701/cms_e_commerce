<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class BrandValidator extends BaseValidator
{
    public static function validateCreate(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'summary'     => 'nullable|string',
            'image'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alias'       => 'nullable|string|max:255',
            'status'      => 'nullable|integer|in:0,1',
        ];
    }

    public static function validateUpdate($id): array
    {
        return [
            'name'        => "required|string|max:255|unique:brands,name,$id",
            'summary'     => 'nullable|string',
            'image'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alias'       => "nullable|string|max:255|unique:brands,alias,$id",
            'status'      => 'nullable|integer|in:0,1',
            'version'     => 'required|integer|min:1',
        ];
    }
}
