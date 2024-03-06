<?php

namespace LaravelSupports\AI\OpenAI\Contracts;

use LaravelSupports\AI\OpenAI\Enums\OpenAITypes;

interface HasOpenAIType
{
    public function getOpenAIType(): OpenAITypes;
}
