<?php

namespace Core\controllers;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected string $validator;

    protected function validate(string $method): void
    {
        if (!class_exists($this->validator)) {
            throw new \RuntimeException("Validator class {$this->validator} not found");
        }

        ($this->validator)::validateMethod($method);
    }
}
