<?php

namespace LaravelSupports\Locale\Contracts;

interface LocaleServiceContract
{

    public function getLocale(): LocaleModel;

    public function setLocale(LocaleModel $model): void;
}