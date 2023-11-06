<?php

namespace LaravelSupports\Crypt\Contracts;

use LaravelSupports\Supports\Requests\Contracts\RequestValueCastContract;

interface CryptContract extends RequestValueCastContract
{
    public function getProperty($val);
}
