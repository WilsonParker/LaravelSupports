<?php

namespace LaravelSupports\Push\Models;

use App\Helpers\PushHelper;
use LaravelSupports\Models\Common\BaseModel;

class MobilePushTokenModel extends BaseModel
{
    public bool $incrementing = true;
    protected $table = 'mobile_push_token';

}
