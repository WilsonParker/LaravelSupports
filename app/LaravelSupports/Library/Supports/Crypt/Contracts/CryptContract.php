<?php

namespace App\LaravelSupports\Library\Supports\Crypt\Contracts;

use App\LaravelSupports\Library\Supports\Requests\Contracts\RequestValueCastContract;

interface CryptContract extends RequestValueCastContract
{
    public function getProperty($val);
}
