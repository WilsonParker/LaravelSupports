<?php

namespace LaravelSupports\SMS\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelSupports\SMS\Helpers\SMSHelper;

class SMSModel extends Model
{
    protected $table = 'fly_sms';
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
