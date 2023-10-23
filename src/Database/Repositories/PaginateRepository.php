<?php

namespace LaravelSupports\Database\Repositories;

use LaravelSupports\Database\Repositories\Contracts\PaginateRepositoryContract;
use Illuminate\Contracts\Pagination\Paginator;

class PaginateRepository extends BaseRepository implements PaginateRepositoryContract
{

    public function paginate(int $page = 1, int $size = 10): Paginator
    {
        return $this->model->paginate($size);
    }
}
