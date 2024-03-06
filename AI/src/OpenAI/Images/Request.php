<?php

namespace LaravelSupports\AI\OpenAI\Images;

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
            'prompt' => [
                'required',
                'string',
                'max:1000',
            ],
            'size' => [
                'required',
                'in:' . collect(ImageSize::cases())->map(fn($case) => $case->value)->implode(',')
            ],
            'n' => [
                'nullable',
                'numeric',
                'min:1',
                'max:10',
            ],
            'response_format' => [
                'nullable',
                'in:url,b64_json',
            ],
            'user' => [
                'nullable',
                'string',
                'max:255',
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
            if ($prompt->getOpenAIType() !== OpenAITypes::Image) {
                $validator->errors()->add('message', 'This is an incorrect type');
            }
        });
    }
}
