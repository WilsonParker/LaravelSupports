<?php


namespace LaravelSupports\Libraries\Pay\ImPort\Response;



use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseReadyObject;
use LaravelSupports\Libraries\Pay\Common\Exception\PaymentException;

/**
 * ImPort ontime 결제 시 제공되는 response 객체
 *
 * {
 * "code": 0,
 * "message": "string",
 * "response": {
 * "imp_uid": "string",
 * "merchant_uid": "string",
 * "pay_method": "string",
 * "channel": "pc",
 * "pg_provider": "string",
 * "pg_tid": "string",
 * "pg_id": "string",
 * "escrow": true,
 * "apply_num": "string",
 * "bank_code": "string",
 * "bank_name": "string",
 * "card_code": "string",
 * "card_name": "string",
 * "card_quota": 0,
 * "card_number": "string",
 * "card_type": "null",
 * "vbank_code": "string",
 * "vbank_name": "string",
 * "vbank_num": "string",
 * "vbank_holder": "string",
 * "vbank_date": 0,
 * "vbank_issued_at": 0,
 * "name": "string",
 * "amount": 0,
 * "cancel_amount": 0,
 * "currency": "string",
 * "buyer_name": "string",
 * "buyer_email": "string",
 * "buyer_tel": "string",
 * "buyer_addr": "string",
 * "buyer_postcode": "string",
 * "custom_data": "string",
 * "user_agent": "string",
 * "status": "ready",
 * "started_at": 0,
 * "paid_at": 0,
 * "failed_at": 0,
 * "cancelled_at": 0,
 * "fail_reason": "string",
 * "cancel_reason": "string",
 * "receipt_url": "string",
 * "cancel_history": [
 * {
 * "pg_tid": "string",
 * "amount": 0,
 * "cancelled_at": 0,
 * "reason": "string",
 * "receipt_url": "string"
 * }
 * ],
 * "cancel_receipt_urls": [
 * "string"
 * ],
 * "cash_receipt_issued": true,
 * "customer_uid": "string",
 * "customer_uid_usage": "issue"
 * }
 * }
 *
 * @param
 * @return
 * @author  dew9163
 * @added   2020/06/17
 * @updated 2020/06/17
 */
class ImPortResponse extends AbstractResponseReadyObject
{
    protected $code;
    protected $message;
    protected $response;
    protected $imp_uid;
    protected $merchant_uid;
    protected $pay_method;
    protected $channel;
    protected $pg_provider;
    protected $pg_tid;
    protected $pg_id;
    protected $escrow;
    protected $apply_num;
    protected $bank_code;
    protected $bank_name;
    protected $card_code;
    protected $card_name;
    protected $card_quota;
    protected $card_number;
    protected $card_type;
    protected $vbank_code;
    protected $vbank_name;
    protected $vbank_num;
    protected $vbank_holder;
    protected $vbank_date;
    protected $vbank_issued_at;
    protected $name;
    protected $amount;
    protected $cancel_amount;
    protected $currency;
    protected $buyer_name;
    protected $buyer_email;
    protected $buyer_tel;
    protected $buyer_addr;
    protected $buyer_postcode;
    protected $customer_name;
    protected $customer_email;
    protected $customer_tel;
    protected $customer_addr;
    protected $customer_postcode;
    protected $custom_data;
    protected $user_agent;
    protected $status;
    protected $started_at;
    protected $paid_at;
    protected $failed_at;
    protected $cancelled_at;
    protected $fail_reason;
    protected $cancel_reason;
    protected $receipt_url;
    protected $cash_receipt_issued;
    protected $customer_uid;
    protected $customer_uid_usage;

    public function getTID()
    {
        return $this->imp_uid;
    }

    public function getUid()
    {
        return $this->merchant_uid;
    }

    public function getCID()
    {
        return $this->pg_id;
    }

    public function getSID()
    {
        return $this->customer_uid;
    }

    public function getPaymentMethodType()
    {
        return $this->pay_method;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getResult()
    {
    }

    public function isSuccess() {
        $isFailedStatus = isset($this->status) && $this->status == 'failed';
        $isFailedCode = $this->code == -1;
        return $isFailedCode || is_null($this->response) || $isFailedStatus;
    }

    /**
     * @param
     * @return void
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/25
     * @updated 2020/06/25
     * @updated 2020/07/13
     * return exception if message not null
     * @updated 2020/07/14
     * return exception if code is -1
     * @updated 2020/07/15
     * return exception if status is 'failed'
     */
    public function bindStd($std)
    {
        parent::bindStd($std->response);
        $this->code = $std->code;
        $this->message = $std->message;
        $this->response = $std->response;
        $this->fail_reason;

        $errorMessage = isset($this->message) ? $this->message : (isset($this->fail_reason) ? $this->fail_reason : '');
        throw_if($this->isSuccess(), new PaymentException($errorMessage));
    }

}
