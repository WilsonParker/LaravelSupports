<?php

namespace App\Library\LaravelSupports\app\Database\Repositories;

use App\Library\LaravelSupports\app\Database\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryContract
{
    protected Model $model;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model) { $this->model = $model; }

    public function index(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function create(array $attribute): Model
    {
        return $this->model->create($attribute);
    }

    public function show($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, array $attribute): bool
    {
        return $this->model->update($attribute);
    }

    public function delete($id): bool
    {
        return $this->show($id)->delete();
    }

}