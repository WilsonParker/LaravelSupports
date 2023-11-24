<?php

namespace LaravelSupports\Locale\Contracts;

use Illuminate\Support\Collection;

interface LocaleRepositoryContract
{
    public function getLocaleByCode(string $code): ?LocaleModel;

    /**
     * @return Collection<LocaleModel>
     */
    public function getLocales(): Collection;

}