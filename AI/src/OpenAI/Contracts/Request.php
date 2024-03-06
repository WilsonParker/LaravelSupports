<?php

namespace LaravelSupports\AI\OpenAI\Contracts;


interface Request
{
    public function rules(): array;

    public function commonRules(): array;
}
