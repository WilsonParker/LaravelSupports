<?php

namespace LaravelSupports\AI\OpenAI\Contracts;


use LaravelSupports\AI\OpenAI\Models\OpenAiKeyStack;

interface ApiRecursiveContract extends ApiContract
{
    public function callApiRecursive(array $attributes, ?OpenAiKeyStack $key = null, ?int $try = 0): array;
}
