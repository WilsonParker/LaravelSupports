<?php

namespace App\Library\LaravelSupports\app\Database\Repositories\Contracts;

interface PaginateRepositoryContract
{
    public function paginate(int $size);

}