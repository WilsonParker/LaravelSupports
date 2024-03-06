<?php

namespace LaravelSupports\AI\Price\Contracts;

interface PriceComposite extends HasPrice
{
    public function isValid(string $type): bool;
}
