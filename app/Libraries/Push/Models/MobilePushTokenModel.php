<?php

namespace LaravelSupports\LibrariesPush\Models;

use App\Helpers\PushHelper;
use App\Models\Common\BaseModel;

class MobilePushTokenModel extends BaseModel
{
    protected string $table = 'mobile_push_token';
    public bool $incrementing = true;

}
