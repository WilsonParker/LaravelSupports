<?php

namespace LaravelSupports\Locale;

use Illuminate\Support\Facades\Session;
use LaravelSupports\Locale\Contracts\LocaleModel;
use LaravelSupports\Locale\Contracts\LocaleRepositoryContract;
use LaravelSupports\Locale\Contracts\LocaleServiceContract;

class LocaleService implements LocaleServiceContract
{
    private string $session = 'locale';
    private string $default = 'US';

    /**
     * @param \LaravelSupports\Locale\Contracts\LocaleRepositoryContract $repository
     */
    public function __construct(private readonly LocaleRepositoryContract $repository) {}

    public function hasLocale(): bool
    {
        return Session::has($this->session);
    }

    public function getLocale(): LocaleModel
    {
        return Session::get($this->session, $this->repository->getLocaleByCode($this->default));
    }

    public function setLocale(LocaleModel $model): void
    {
        Session::forget($this->session);
        Session::remember($this->session, fn() => $model);
        // Session::put($this->session, $model);
    }

    public function setLocaleCode(string $code): void
    {
        Session::forget($this->session);
        Session::remember($this->session, fn() => $this->repository->getLocaleByCode($code));
        // Session::put($this->session, $this->repository->getLocaleByCode($code));
    }
}