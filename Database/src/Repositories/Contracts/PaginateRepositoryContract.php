<?php

namespace LaravelSupports\Database\Repositories\Contracts;

interface PaginateRepositoryContract
{
    public function paginate(int $page = 1, int $size = 10);

}
