<?php

namespace App\Library\LaravelSupports\app\Database\Repositories;

use App\Library\LaravelSupports\app\Database\Repositories\Contracts\PaginateRepositoryContract;
use Illuminate\Contracts\Pagination\Paginator;

class PaginateRepository extends BaseRepository implements PaginateRepositoryContract
{

    public function paginate(int $size = 10) : Paginator
    {
        return $this->model->paginate($size);
    }
}