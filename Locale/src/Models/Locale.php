<?php

namespace LaravelSupports\Locale\Models;

use LaravelSupports\Locale\Contracts\LocaleModel;

class Locale implements LocaleModel {
    protected string $localeCode;
    protected string $localeLanguage;
    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getLocaleLanguage(): string
    {
        return $this->localeLanguage;
    }
}
