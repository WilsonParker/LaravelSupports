<?php

namespace App\LaravelSupports\Library\Push\Models;

use App\Helpers\PushHelper;
use App\LaravelSupports\Models\Common\BaseModel;

class MobilePushTokenModel extends BaseModel
{
    protected string $table = 'mobile_push_token';
    public bool $incrementing = true;

}
