<?php

namespace LaravelSupports\AI\Price\Contracts;

interface HasPrice
{
    public function getInputPrice(): float;

    public function getOutputPrice(): float;
}
