<?php

namespace LaravelSupports\Libraries\Supports\Enums;

use Illuminate\Support\Collection;

trait GetAttributesTrait
{
    public static function getAttributes($attribute = 'value'): Collection
    {
        return collect(self::cases())->map(fn($item) => $item->$attribute);
    }
}
