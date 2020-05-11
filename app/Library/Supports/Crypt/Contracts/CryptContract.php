<?php

namespace App\Library\Supports\Crypt\Contracts;

use App\Library\Supports\Requests\Contracts\RequestValueCastContract;

interface CryptContract extends RequestValueCastContract
{
    public function getProperty($val);
}
