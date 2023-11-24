<?php

namespace LaravelSupports\Http\Requests\Contracts;

interface RequestValueCastingPossible
{
    public function castValue(string $key, $val);
}
