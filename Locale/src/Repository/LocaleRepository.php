<?php

namespace LaravelSupports\Locale\Repository;

use Illuminate\Support\Collection;
use LaravelSupports\Locale\Contracts\LocaleModel;
use LaravelSupports\Locale\Contracts\LocaleRepositoryContract;
use LaravelSupports\Locale\Models\Locale;

class LocaleRepository implements LocaleRepositoryContract
{

    public function getLocaleByCode(string $code): ?LocaleModel
    {
        return Locale::whereCode($code)->first();
    }

    public function getLocales(): Collection
    {
        return Locale::all();
    }
}