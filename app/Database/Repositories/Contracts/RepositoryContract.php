<?php

namespace App\Library\LaravelSupports\app\Database\Repositories\Contracts;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryContract
{
    public function index(): Collection;

    public function create(array $attribute): BaseModel;

    public function show($id): BaseModel;

    public function update($id, array $attribute): bool;

    public function delete($id): bool;

}