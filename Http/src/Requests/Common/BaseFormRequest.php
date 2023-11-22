<?php

namespace LaravelSupports\Http\Requests\Common;

abstract class BaseFormRequest extends BaseRequest
{

    protected bool $isFailedRedirect = true;

}
