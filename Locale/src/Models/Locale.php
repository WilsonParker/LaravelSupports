<?php

namespace LaravelSupports\Locale\Models;

use LaravelSupports\Locale\Contracts\LocaleModel;
use V2News\Models\BaseCodeModel;

class Locale extends BaseCodeModel implements LocaleModel
{
    protected $table = 'locales';
    protected $guarded = [];

    public function getLocaleCode(): string
    {
        return $this->code;
    }

    public function getLocaleLanguage(): string
    {
        return $this->launguage;
    }
}
