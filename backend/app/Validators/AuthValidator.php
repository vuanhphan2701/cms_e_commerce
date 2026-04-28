<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class AuthValidator extends BaseValidator
{
    /**
     * Validation rules for user registration.
     */
    public static function validateRegister(): array
    {
        return [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Validation rules for user login.
     */
    public static function validateLogin(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }
}
