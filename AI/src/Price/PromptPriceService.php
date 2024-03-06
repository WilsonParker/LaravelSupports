<?php

namespace LaravelSupports\AI\Price;


use LaravelSupports\AI\Price\Contracts\HasPrice;

class PromptPriceService
{
    /**
     * @var array<\LaravelSupports\AI\Price\Contracts\HasPrice> $composites
     */
    public function __construct(
        private readonly array $composites,
    )
    {
    }

    /**
     * @throws \LaravelSupports\AI\Price\Exceptions\InvalidPriceTypeException
     */
    public function getPrice(string $type): HasPrice
    {
        foreach ($this->composites as $composite) {
            if ($composite->isValid($type)) {
                return $composite;
            }
        }
        throw new Exceptions\InvalidPriceTypeException();
    }
}
