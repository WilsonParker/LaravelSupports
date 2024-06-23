<?php

namespace LaravelSupports\AI\OpenAI\Chat;

use LaravelSupports\AI\OpenAI\Contracts\HasPrice;

enum Models: string implements HasPrice
{
    case GPT_4 = 'gpt-4';
    case GPT_4_32k = 'gpt-4-32k';
    case GPT_3_5_turbo = 'gpt-3.5-turbo';
    case GPT_3_5_turbo_16k = 'gpt-3.5-turbo-16k';

    public function getMaxToken(): int
    {
        return match ($this) {
            self::GPT_3_5_turbo => 4096,
            self::GPT_3_5_turbo_16k => 4096,
            self::GPT_4 => 4096,
            self::GPT_4_32k => 4096,
        };
    }

    // input price
    public function getPrice(): float
    {
        // 	$0.002 / 1K tokens
        return match ($this) {
            self::GPT_3_5_turbo => 0.0015,
            self::GPT_3_5_turbo_16k => 0.003,
            self::GPT_4 => 0.03,
            self::GPT_4_32k => 0.06,
        };
    }

    public function perToken(): float
    {
        return $this->getPrice() / 1024;
    }


}
