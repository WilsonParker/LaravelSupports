<?php

namespace LaravelSupports\AI\OpenAI\Chat;

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
            'messages' => [
                'required',
                'string',
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
            'max_tokens' => [
                'nullable',
                'numeric',
                'min:1',
                'max:2048',
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
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /**
             * @var HasOpenAIType $prompt
             * */
            $prompt = $this->route('prompt');
            if (isset($prompt) && $prompt->getOpenAIType() !== OpenAITypes::Chat) {
                $validator->errors()->add('message', 'This is an incorrect type');
            }
        });
    }
}
