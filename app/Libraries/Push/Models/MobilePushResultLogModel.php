<?php


namespace LaravelSupports\LibrariesPush\Models;

use Illuminate\Http\Request;

class MobilePushResultLogModel extends AbstractMobilePushModel
{
    protected string $table = 'mobile_push_result_log';

    // @Override
    function setSerializeData($json)
    {
        $this->serialize_data = $json;
    }

    // @Override
    function execute()
    {
        return $this->save();
    }
}
