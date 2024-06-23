<?php

namespace LaravelSupports\AI\OpenAI\Completion;

use OpenAI\Laravel\Facades\OpenAI;
use LaravelSupports\AI\OpenAI\Abstracts\AbstractApiService;

class ApiService extends AbstractApiService
{
    public function call(array $attributes, ?string $key = null): array
    {
        $api = $key ?
            \OpenAI::client($key, config('openai.organization'))->completions()
            : OpenAI::completions();
        return $api->create($this->validate($this->getRules(), $attributes))->toArray();
    }

    public function getRules(): array
    {
        $request = new Request();
        $rules = $request->rules();
        $rules['prompt'] = [
            'required',
            'array',
        ];
        return $rules;
    }
}
