<?php

namespace LaravelSupports\Locale;

use Illuminate\Support\Facades\Session;
use LaravelSupports\Locale\Contracts\LocaleModel;
use LaravelSupports\Locale\Contracts\LocaleRepositoryContract;
use LaravelSupports\Locale\Contracts\LocaleServiceContract;

class LocaleService implements LocaleServiceContract
{
    private string $session = 'locale';
    private string $default = 'en';

    /**
     * @param \LaravelSupports\Locale\Contracts\LocaleRepositoryContract $repository
     */
    public function __construct(private readonly LocaleRepositoryContract $repository) {}

    public function getLocale(): LocaleModel
    {
        return Session::get($this->session, $this->repository->getLocaleByCode($this->default));
    }

    public function setLocale(LocaleModel $model): void
    {
        Session::put($this->session, $model);
    }

    public function setLocaleCode(string $code): void
    {
        Session::put($this->session, $this->repository->getLocaleByCode($code));
    }
}