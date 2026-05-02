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

    /**
     * Validation rules for forgot password.
     */
    public static function validateForgotPassword(): array
    {
        return [
            'email' => 'required|string|email',
        ];
    }

    /**
     * Validation rules for reset password.
     */
    public static function validateResetPassword(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Validation rules for verify OTP.
     */
    public static function validateVerifyOtp(): array
    {
        return [
            'email' => 'required|string|email',
            'otp' => 'required|string|size:6',
        ];
    }

    /**
     * Validation rules for resend verification email.
     */
    public static function validateResendVerification(): array
    {
        return [
            'email' => 'required|string|email',
        ];
    }
}
