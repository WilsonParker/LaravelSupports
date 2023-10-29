<?php

namespace LaravelSupports\Libraries\Supports\Http\Requests\Contracts;

interface RequestValueCastingPossible
{
    public function castValue(String $key, $val);
}
