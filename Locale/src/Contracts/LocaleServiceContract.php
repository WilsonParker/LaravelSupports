<?php

namespace LaravelSupports\Locale\Contracts;

interface LocaleServiceContract
{
    public function hasLocale(): bool;

    public function getLocale(): LocaleModel;

    public function setLocale(LocaleModel $model): void;

    public function setLocaleCode(string $code): void;
}