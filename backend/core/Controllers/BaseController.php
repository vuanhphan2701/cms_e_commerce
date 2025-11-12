<?php

namespace Core\Controllers;
use Core\Response;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    // The validator class associated with the controller
    protected string $validator;

    protected function validate(string $method)
    {

        if (!class_exists($this->validator)) {
            Response::error("Validator class {$this->validator} does not exist.", 500);
           // throw new \Exception("Validator class {$this->validator} does not exist.");
        }

        return ($this->validator)::validateMethod($method);
    }
}
