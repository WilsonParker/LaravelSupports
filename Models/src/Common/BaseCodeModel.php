<?php

namespace LaravelSupports\Models\Common;

abstract class BaseCodeModel extends BaseModel
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'code';
}
