<?php

namespace App\Library\LaravelSupports\app\Database\Repositories\Contracts;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function index(): Collection;

    public function store(array $attribute): Model;

    public function show(Model $model): Model;

    public function showById($id): Model;

    public function update(Model $model, array $attribute): bool;

    public function updateById($id, array $attribute): bool;

    public function delete(Model $model): bool;

    public function deleteById($id): bool;

}