<?php

namespace LaravelSupports\AI\OpenAI\Contracts;


use RecipeP\Models\OpenAI\OpenAiKeyStack;

interface ApiRecursiveContract extends ApiContract
{
    public function callApiRecursive(array $attributes, ?OpenAiKeyStack $key = null, ?int $try = 0): array;
}
