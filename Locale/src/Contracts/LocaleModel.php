<?php

namespace LaravelSupports\Locale\Contracts;

interface LocaleModel
{
    public function getLocaleCode(): string;

    public function getLocaleLanguage(): string;
}