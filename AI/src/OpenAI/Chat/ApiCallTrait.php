<?php

namespace LaravelSupports\AI\OpenAI\Chat;

use OpenAI\Laravel\Facades\OpenAI;

trait ApiCallTrait
{
    public function call(array $attributes, ?string $key = null): array
    {
        $api = $key ?
            \OpenAI::client($key, config('openai.organization'))->chat()
            : OpenAI::chat();
        return $api->create($this->cast($this->validate($this->getRules(), $attributes)))->toArray();
    }

    public function getRules(): array
    {
        $request = new Request();
        $rules = $request->rules();
        $rules['messages'] = [
            'required',
            'array',
        ];
        return $rules;
    }
}
