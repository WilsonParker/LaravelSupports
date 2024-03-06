<?php

namespace LaravelSupports\AI\Price\Composites;

use LaravelSupports\AI\OpenAI\Enums\OpenAITypes;
use LaravelSupports\AI\Price\Contracts\PriceComposite;

class DallE implements PriceComposite
{

    public function getInputPrice(): float
    {
        // TODO: Implement getInputPrice() method.
    }

    public function getOutputPrice(): float
    {
        // TODO: Implement getOutputPrice() method.
    }

    public function isValid(string $type): bool
    {
        return $type == OpenAITypes::Image;
    }
}
