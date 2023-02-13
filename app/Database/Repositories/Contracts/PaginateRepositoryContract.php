<?php

namespace App\Library\LaravelSupports\app\Database\Repositories\Contracts;

interface PaginateRepositoryContract extends RepositoryContract
{
    public function paginate(int $size);

}