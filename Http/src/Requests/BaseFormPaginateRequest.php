<?php

namespace LaravelSupports\Http\Requests;

abstract class BaseFormPaginateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'string',
            ],
            'size' => [
                'nullable',
                'integer',
            ],
            ...$this->additionalRules()
        ];
    }

    public function additionalRules(): array
    {
        return [];
    }
}
