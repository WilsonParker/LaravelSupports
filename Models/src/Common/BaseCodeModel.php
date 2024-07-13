<?php

namespace App\Modules\Supports\Models\src\Common;

use LaravelSupports\Models\Common\BaseModel;

abstract class BaseCodeModel extends BaseModel
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'code';
}
