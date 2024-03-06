<?php

namespace LaravelSupports\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface AuthRepositoryContract
{
    public function findOrFail(int $id): Authenticatable|Model;
}
