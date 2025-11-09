<?php

namespace Core\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class BaseValidator
{
    /**
     * Validate request theo rules method
     *
     * @param string $method
     *
     * @throws ValidationException|RuntimeException
     */
    public static function validateMethod(string $method): void
    {
        if (!method_exists(static::class, $method)) {
            throw new RuntimeException("Method {$method} not found in " . static::class);
        }

        $rules = static::$method();
        $data = request()->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
