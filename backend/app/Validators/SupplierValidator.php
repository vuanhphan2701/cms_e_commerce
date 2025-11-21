<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class SupplierValidator extends BaseValidator
{
    public static function validateCreate(): array
    {
        return [
            'name'        => 'required|string|max:150',
            'summary'     => 'nullable|string',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
            'image'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alias'       => 'nullable|string|max:255|unique:suppliers,alias',
            'status'      => 'nullable|integer|in:0,1',
        ];
    }
        public static function validateUpdate(int $id): array
    {
        return [
            'name'        => 'required|string|max:150',
            'summary'     => 'nullable|string',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
            'image'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alias'       => "nullable|string|max:255|unique:suppliers,alias,$id",
            'status'      => 'nullable|integer|in:0,1',
            'version'     => 'required|integer|min:1',
        ];
    }
}
