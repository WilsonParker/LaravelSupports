<?php

namespace LaravelSupports\Database\Repositories\Contracts;

interface Sortable
{
    public function sort($query, $sort): void;
}
