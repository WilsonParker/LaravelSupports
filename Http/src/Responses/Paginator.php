<?php

namespace LaravelSupports\Http\Responses;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    private \Illuminate\Contracts\Pagination\Paginator $paginator;

    public function __construct()
    {
    }

    public function transfer(\Illuminate\Contracts\Pagination\Paginator $paginator): self
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'items' => $this->paginator->items->toArray(),
            'last_page' => $this->paginator->lastPage(),
            'per_page' => $this->paginator->perPage(),
            'from' => $this->paginator->firstItem(),
            'to' => $this->paginator->lastItem(),
            'total' => $this->paginator->total(),
        ];
    }
}
