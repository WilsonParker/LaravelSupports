<?php

namespace LaravelSupports\Http\Requests\Exceptions;

class ValidationException extends \Illuminate\Validation\ValidationException
{

    /*public function errors()
    {
        return collect($this->validator->errors())->mapWithKeys(function ($errors, $key) {
            return [$key => $errors[0]];
        });
    }*/
}
