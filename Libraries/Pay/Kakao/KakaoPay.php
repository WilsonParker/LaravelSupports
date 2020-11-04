<?php


namespace LaravelSupports\Libraries\Pay\Kakao;



use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractPayService;
use LaravelSupports\Libraries\Pay\Kakao\Response\KakaoResponseApproveObject;
use LaravelSupports\Libraries\Pay\Kakao\Response\KakaoResponseReadyObject;

class KakaoPay extends AbstractPayService
{

    protected $webHookURL = 'https://api2.flybook.kr/v3/membership/pay/callback';
    protected $host = 'https://kapi.kakao.com';

    protected function init()
    {
    }

    /**
     * @inheritDoc
     */
    protected function getHeaders()
    {
        return [
            "Authorization" => "KakaoAK " . $this->getAdminKey()
        ];
    }

    public function getReadyData()
    {
        return [
            'cid' => $this->getCID(),
            'partner_order_id' => $this->getPartnerOrderID(),
            'partner_user_id' => $this->getPartnerUserID(),
            'item_name' => $this->getItemName(),
            'quantity' => $this->getQuantity(),
            'total_amount' => $this->getTotalAmount(),
            'vat_amount' => $this->getVatAmount(),
            'tax_free_amount' => $this->getTaxFreeAmount(),
            'approval_url' => $this->getApprovalUrl(),
            'cancel_url' => $this->getCancelUrl(),
            'fail_url' => $this->getFailUrl(),
        ];
    }

    public function getApproveData()
    {
        return [
            'cid' => $this->getCID(),
            'tid' => $this->payment->getTID(),
            'partner_order_id' => $this->getPartnerOrderID(),
            'partner_user_id' => $this->getPartnerUserID(),
            'pg_token' => $this->getToken(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSubscribeData()
    {
        return [
            'cid' => $this->getCID(),
            'sid' => $this->getSID(),
            'partner_order_id' => $this->getPartnerOrderID(),
            'partner_user_id' => $this->getPartnerUserID(),
            'item_name' => $this->getItemName(),
            'quantity' => '1',
            'total_amount' => $this->getTotalAmount(),
            'vat_amount' => '0',
            'tax_free_amount' => '0',
        ];
    }


    public function ready()
    {
        $result = $this->call("/v1/payment/ready", $this->getReadyData());
        $obj = new KakaoResponseReadyObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function approve()
    {
        $result = $this->call("/v1/payment/approve", $this->getApproveData());
        $obj = new KakaoResponseApproveObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function subscription()
    {
        $result = $this->call("/v1/payment/subscription", $this->getSubscribeData());
        $obj = new KakaoResponseApproveObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function cancel()
    {
        return $this->call("/v1/payment/cancel", []);
    }

    public function order()
    {
        return $this->call("/v1/payment/order", []);
    }

    public function inactive()
    {
        return $this->call("/v1/payment/inactive", []);
    }

    public function paymentComplete($result)
    {

    }

    public function getAdminKey()
    {
        return config('values.payment.kakao.key');
    }

    public function getCID()
    {
        return $this->payment->isSubscribe() ? config('values.payment.kakao.autopay_tid') : config('values.payment.kakao.tid');
    }

    public function getCIDSecret()
    {
        return null;
    }

    public function getSID()
    {
        return $this->payment->getSID();
    }

    protected function getCallbackUrl($url)
    {
        return "{$this->webHookURL}{$url}?type=kakao_pay&payload={$this->getPayload()}";
    }

    /**
     * @inheritDoc
     */
    public function storeSubscribeUser()
    {
    }

}
