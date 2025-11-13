<?php

namespace Core\Validators;

use Core\Response;

use Illuminate\Support\Facades\Validator;

class BaseValidator
{
    /**
     * validateMethod
     * @param string $method
     * @throws \Exception
     * @return array
     */
    public static function validateMethod(string $method)
    {

        if (!method_exists(static::class, $method)) {
            throw new \Exception("Validation method {$method} does not exist in " . static::class);
        }

        // Get the validation rules from the specified method
        $rule = static::{$method}();
       // dd($rule);
        $data = request()->all();
        $validator = Validator::make($data, $rule);

        // Check if validation fails
        if ($validator->fails()) {
            Response::error($validator->errors()->all(), 422)->throwResponse();
        }

        return $validator->validated();
    }
}
