<?php

namespace App\Library\LaravelSupports\app\Database\Repositories;

use App\Library\LaravelSupports\app\Database\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(protected Model $model) {}

    public function index(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function store(array $attribute): Model
    {
        return $this->model->create($attribute);
    }

    public function show(Model $model, array|string $with = '', array|string $select = '*'): Model
    {
        return $this->showById($model->getKey(), $with, $select);
    }

    public function update(Model $model, array $attribute): bool
    {
        return $this->updateById($model->getKey(), $attribute);
    }

    public function delete(Model $model): bool
    {
        return $this->deleteById($model->getKey());
    }

    public function showById($id, array|string $with = '', array|string $select = '*'): Model
    {
        return $this->model->with($with)->select($select)->findOrFail($id);
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