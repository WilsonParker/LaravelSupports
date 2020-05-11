<?php

namespace App\LaravelSupports\Library\Supports\Http\Requests\Contracts;

interface RequestValueCastable
{
    public function castValue(String $key, $val);
}
