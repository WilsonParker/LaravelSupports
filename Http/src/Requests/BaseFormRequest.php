<?php

namespace LaravelSupports\Http\Requests;

abstract class BaseFormRequest extends BaseRequest
{

    protected bool $isFailedRedirect = true;

}
