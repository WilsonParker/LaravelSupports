<?php

namespace LaravelSupports\AI\OpenAI\Completion;

use Illuminate\Foundation\Http\FormRequest;
use LaravelSupports\AI\OpenAI\Contracts\HasOpenAIType;
use LaravelSupports\AI\OpenAI\Enums\OpenAITypes;
use LaravelSupports\AI\OpenAI\Traits\HasCommonRules;

class Request extends FormRequest implements \LaravelSupports\AI\OpenAI\Contracts\Request
{
    use HasCommonRules;

    public function rules(): array
    {
        return [
            'model' => [
                'required',
                'in:' . collect(Models::cases())->map(fn($case) => $case->value)->implode(',')
            ],
            'prompt' => [
                'nullable',
                'string'
            ],
            'suffix' => [
                'nullable',
                'string'
            ],
            'max_tokens' => [
                'nullable',
                'integer',
                'min:1',
                'max:2048',
            ],
            'temperature' => [
                'nullable',
                'numeric',
                'min:0',
                'max:2',
            ],
            'top_p' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
            'n' => [
                'nullable',
                'numeric',
                'min:1',
                'max:5',
            ],
            'stream' => [
                'nullable',
                'boolean',
            ],
            'logprobs' => [
                'nullable',
                'numeric',
                'max:5',
            ],
            'echo' => [
                'nullable',
                'boolean',
            ],
            'stop' => [
                'nullable',
                'string',
                'array',
            ],
            'presence_penalty' => [
                'nullable',
                'numeric',
                'min:-2',
                'max:2',
            ],
            'frequency_penalty' => [
                'nullable',
                'numeric',
                'min:-2',
                'max:2',
            ],
            'best_of' => [
                'nullable',
                'numeric',
                'gt:n',
            ],
            'logit_bias' => [
                'nullable',
                'json',
            ],
            'user' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /**
             * @var HasOpenAIType $prompt
             * */
            $prompt = $this->route('prompt');
            if ($prompt->getOpenAIType() !== OpenAITypes::Completion) {
                $validator->errors()->add('message', 'This is an incorrect type');
            }
        });
    }
}
