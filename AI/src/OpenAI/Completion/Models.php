<?php

namespace LaravelSupports\AI\OpenAI\Completion;

use LaravelSupports\AI\OpenAI\Contracts\HasPrice;

enum Models: string implements HasPrice
{
    case TEXT_DAVINCI_003 = "text-davinci-003";
    case TEXT_DAVINCI_002 = "text-davinci-002";
    case TEXT_CURIE_001 = "text-curie-001";
    case TEXT_BABBAGE_001 = "text-babbage-001";
    case TEXT_ADA_001 = "text-ada-001";

    public function getPrice(): float
    {
        return match ($this) {
            self::TEXT_DAVINCI_003 => 0.0200,
            self::TEXT_DAVINCI_002 => 0.0200,
            self::TEXT_CURIE_001 => 0.0020,
            self::TEXT_BABBAGE_001 => 0.0005,
            self::TEXT_ADA_001 => 0.0004,
        };
    }

    public function perToken(): float
    {
        return $this->getPrice() / 1024;
    }

    public function getMaxToken(): int
    {
        return 4096;
    }
}
