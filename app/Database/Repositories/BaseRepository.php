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

    public function store(array $attribute): Model
    {
        return $this->model->create($attribute);
    }

    public function show(Model $model): Model
    {
        return $this->model->findOrFail($model->getKey());
    }

    public function update(Model $model, array $attribute): bool
    {
        return $this->show($model)->update($attribute);
    }

    public function delete(Model $model): bool
    {
        return $this->show($model->getKey())->delete();
    }

    public function showById($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function updateById($id, array $attribute): bool
    {
        return $this->showById($id)->update($attribute);
    }

    public function deleteById($id): bool
    {
        return $this->showById($id)->delete();
    }
}