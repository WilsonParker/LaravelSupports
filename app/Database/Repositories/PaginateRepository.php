<?php

namespace App\Library\LaravelSupports\app\Database\Repositories;

use App\Library\LaravelSupports\app\Database\Repositories\Contracts\PaginateRepositoryContract;
use App\Library\LaravelSupports\app\Database\Repositories\Contracts\RepositoryContract;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PaginateRepository extends BaseRepository implements PaginateRepositoryContract
{

    public function paginate(int $size) : Paginator
    {
        return $this->model->paginate($size);
    }
}