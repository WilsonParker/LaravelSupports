<?php

namespace App\Library\LaravelSupports\app\Database\Repositories\Contracts;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function index(): Collection;

    public function create(array $attribute): Model;

    public function show($id): Model;

    public function update($id, array $attribute): bool;

    public function delete($id): bool;

}