<?php

namespace LaravelSupports\AI\OpenAI\Abstracts;

use Illuminate\Support\Facades\Validator;
use LaravelSupports\AI\OpenAI\Contracts\ApiContract;
use LaravelSupports\AI\OpenAI\Contracts\Request;

abstract class AbstractApiService implements ApiContract
{
    protected array $cast = [
        'max_tokens' => 'int',
        'temperature' => 'float',
        'top_p' => 'float',
        'presence_penalty' => 'float',
        'frequency_penalty' => 'float',
        'best_of' => 'int',
        'n' => 'int',
        'stream' => 'bool',
        'logprobs' => 'int',
        'stop' => 'array',
        'echo' => 'bool',
    ];

    public function validate(Request|array $rules, array $attributes): array
    {
        if ($rules instanceof Request) {
            $rules = $rules->rules();
        }
        $validator = Validator::make($attributes, $rules);
        return $validator->validate();
    }

    abstract public function getRules(): array;

    protected function cast(array $attributes): array
    {
        foreach ($this->cast as $key => $type) {
            if (isset($attributes[$key])) {
                settype($attributes[$key], $type);
            }
        }
        return $attributes;
    }
}
