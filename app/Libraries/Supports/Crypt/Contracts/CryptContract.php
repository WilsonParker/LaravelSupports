<?php

namespace LaravelSupports\Libraries\Supports\Crypt\Contracts;

use LaravelSupports\Libraries\Supports\Requests\Contracts\RequestValueCastContract;

interface CryptContract extends RequestValueCastContract
{
    public function getProperty($val);
}
