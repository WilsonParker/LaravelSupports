<?php

namespace LaravelSupports\Database\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function list(): Collection;

    public function create(array $attribute): Model;

    public function update(Model $model, array $attribute): bool;

    public function delete(Model $model): bool;

}
