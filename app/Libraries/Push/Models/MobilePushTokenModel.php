<?php

namespace LaravelSupports\LibrariesPush\Models;

use App\Helpers\PushHelper;
use LaravelSupports\Models\Common\BaseModel;

class MobilePushTokenModel extends BaseModel
{
    protected string $table = 'mobile_push_token';
    public bool $incrementing = true;

}
