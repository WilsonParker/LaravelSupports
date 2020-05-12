<?php

namespace LaravelSupports\Libraries\Supports\Http\Requests\Contracts;

interface RequestValueCastable
{
    public function castValue(String $key, $val);
}
