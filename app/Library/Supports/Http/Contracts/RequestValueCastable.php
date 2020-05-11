<?php

namespace App\Library\Supports\Http\Contracts;

interface RequestValueCastable
{
    public function castValue(String $key, $val);
}
