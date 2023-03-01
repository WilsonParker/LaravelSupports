<?php

namespace LaravelSupports\Database\Repositories\Contracts;

interface PaginateRepositoryContract
{
    public function paginate(int $size);

}