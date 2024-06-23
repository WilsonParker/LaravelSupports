<?php

namespace LaravelSupports\AI\OpenAI\Images;

use LaravelSupports\AI\OpenAI\Contracts\HasPrice;

enum ImageSize: string implements HasPrice
{
    case s256 = '256x256';
    case s512 = '512x512';
    case s1024 = '1024x1024';

    public function getPrice(): float
    {
        // per image
        return match ($this) {
            self::s256 => 0.016,
            self::s512 => 0.018,
            self::s1024 => 0.020,
        };
    }

    public function perToken(): float
    {
        return $this->getPrice();
    }

    public function getMaxToken(): int
    {
        return 1;
    }
}
