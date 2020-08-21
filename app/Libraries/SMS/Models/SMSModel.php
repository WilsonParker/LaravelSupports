<?php

namespace LaravelSupports\Libraries\SMS\Models;

use LaravelSupports\Libraries\SMS\Helpers\SMSHelper;
use Illuminate\Database\Eloquent\Model;

class SMSModel extends Model
{
    protected $table = 'sms';
    protected $fillable = [];
    protected $guarded = [];

    public function bindData($data)
    {
        $this->template_code = $data[SMSHelper::KEY_TEMPLATE_CODE];
        $this->phone = $data[SMSHelper::KEY_PHONE];
        $this->message = $data[SMSHelper::KEY_MESSAGE];
        $this->cmid = $data['cmid'];
        $this->result_code = $data['result_code'];
        $this->result = $data['result_message'];
    }

}
