<?php

namespace LaravelSupports\AI\OpenAI\Contracts;


interface ApiContract
{
    public function call(array $attributes, ?string $key = null): array;

    public function validate(array $rules, array $attributes): array;
}
