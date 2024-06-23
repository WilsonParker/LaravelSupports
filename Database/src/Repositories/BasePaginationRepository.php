<?php

namespace LaravelSupports\Database\Repositories;

use Illuminate\Contracts\Pagination\Paginator;

class BasePaginationRepository extends BaseRepository
{
    public function paginate(array $attributes, callable $sortCallback): Paginator
    {
        return $this->getSearchQuery($this->model, $attributes)
            ->when($attributes['sort'] ?? null, function ($query) use ($attributes, $sortCallback) {
                $sortCallback($query);
            })
            ->paginate($attributes['size'] ?? 10, '*', $attributes['page'] ?? 1);
    }

}
