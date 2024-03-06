<?php

namespace App\Modules\Supports\Http\src\Requests;

use LaravelSupports\Http\Requests\BaseRequest;

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
