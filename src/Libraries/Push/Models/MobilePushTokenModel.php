<?php

namespace LaravelSupports\Libraries\Push\Models;

use App\Helpers\PushHelper;
use LaravelSupports\Models\Common\BaseModel;

class MobilePushTokenModel extends BaseModel
{
    protected $table = 'mobile_push_token';
    public bool $incrementing = true;

}
