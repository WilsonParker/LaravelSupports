<?php

namespace LaravelSupports\AI\OpenAI\Traits;

trait HasCommonRules
{

    public function commonRules(): array
    {
        return [
            "order" => [
                'nullable',
                'string',
            ],
            "max_tokens" => [
                'required',
                'numeric',
                'min:1',
                'max:2048',
            ],
            'recipe_language' => [
                'nullable',
                'string',
            ],
            'recipe_tone' => [
                'nullable',
                'string',
            ],
            'recipe_writing_style' => [
                'nullable',
                'string',
            ],
        ];
    }
}
