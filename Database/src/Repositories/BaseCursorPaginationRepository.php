<?php

namespace LaravelSupports\Database\Repositories;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Pagination\CursorPaginator;

class BaseCursorPaginationRepository extends BaseRepository
{
    public function paginate(array $attributes, ?callable $sortCallback = null, ?callable $whereCallback = null): CursorPaginator
    {
        return $this->paginateQuery(
            $this->getSearchQuery($this->model, $attributes)
                ->when(isset($attributes['sort']) || isset($sortCallback), fn($query) => $sortCallback($query, $attributes['sort'] ?? null))
                ->when($whereCallback, fn($query) => $whereCallback($query)),
            $attributes,
        );
    }

    public function paginateQuery(BuilderContract $builder, array $attributes): CursorPaginator
    {
        return $builder->cursorPaginate($attributes['size'] ?? 10, '*', 'page', $attributes['page'] ?? null);
    }

}
