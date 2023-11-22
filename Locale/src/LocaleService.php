<?php

namespace LaravelSupports\Locale;

use LaravelSupports\Locale\Contracts\LocaleModel;
use LaravelSupports\Locale\Contracts\LocaleRepositoryContract;
use LaravelSupports\Locale\Contracts\LocaleServiceContract;

class LocaleService implements LocaleServiceContract
{
    private string $default = 'EN';

    /**
     * @param \LaravelSupports\Locale\Contracts\LocaleRepositoryContract $repository
     */
    public function __construct(private readonly LocaleRepositoryContract $repository) {}

    public function getLocale(): LocaleModel
    {
        dd($this->repository->getLocaleByCode($this->default));
        return session('locale', $this->repository->getLocaleByCode($this->default));
    }

    public function setLocale(LocaleModel $model): void
    {
        session('locale', $model);
    }
}