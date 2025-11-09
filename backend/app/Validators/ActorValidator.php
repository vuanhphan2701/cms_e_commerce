<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class ActorValidator extends BaseValidator
{
    public static function validateCreate(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'version' => ['required', 'integer', 'min:1'],
        ];
    }
}
