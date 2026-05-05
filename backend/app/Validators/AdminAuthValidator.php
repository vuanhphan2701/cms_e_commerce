<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class AdminAuthValidator extends BaseValidator
{
    /**
     * Validation rules for admin registration.
     */
    public static function validateRegister(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:super_admin,moderator,finance',
        ];
    }

    /**
     * Validation rules for admin login.
     */
    public static function validateLogin(): array

    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'two_factor_code' => 'nullable|string|size:6',
            'setup_secret' => 'nullable|string',
        ];
    }
}
