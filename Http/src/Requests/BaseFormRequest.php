<?php

namespace LaravelSupports\Http\Requests;

abstract class BaseFormRequest extends BaseRequest
{

    protected bool $isFailedRedirect = true;

    protected function in(array $cases)
    {
        return 'in:' . implode(',', $cases);
    }

}
