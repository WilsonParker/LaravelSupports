<?php

namespace LaravelSupports\AI\OpenAI\Images;

use OpenAI\Laravel\Facades\OpenAI;
use LaravelSupports\AI\OpenAI\Abstracts\AbstractApiService;

class ApiService extends AbstractApiService
{
    public function call(array $attributes, ?string $key = null): array
    {
        $api = $key ?
            \OpenAI::client($key, config('openai.organization'))->images()
            : OpenAI::images();
        $attributes['response_format'] = $attributes['response_format'] ?? ResponseFormat::URL->value;
        return $api->create($this->validate($this->getRules(), $attributes))->toArray();
    }

    public function getRules(): array
    {
        $request = new Request();
        return $request->rules();
    }
}
