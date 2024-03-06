<?php

namespace LaravelSupports\AI\OpenAI\Contracts;

interface HasPrice
{
    public function getPrice(): float;

    public function perToken(): float;

    public function getMaxToken(): int;
}
