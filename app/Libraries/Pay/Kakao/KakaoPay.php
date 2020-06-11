<?php


namespace LaravelSupports\Libraries\Pay\Kakao;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractPayService;
use App\Models\Membership\MembershipPaymentModel;
use App\Models\Payments\PaymentModuleModel;
use LaravelSupports\Libraries\Codes\StringCodeService;

class KakaoPay extends AbstractPayService
{

    protected string $webHookURL = 'http://test.api2.flybook.kr';
    private string $payload;

    protected function init()
    {
        $service = new StringCodeService(64);
        $this->payload = $service->createCode();
    }


    public function getReadyData()
    {
        // TODO: Implement getReadyData() method.
    }

    public function getPayData()
    {
        return [
            'key' => $this->getAdminKey(),
            'payload' => $this->getPayload(),
            'coupon' => $this->getCouponCode(),
            'cid' => $this->getCID(),
            'partner_order_id' => $this->price->getID(),
            'partner_user_id' => $this->member->getID(),
            'item_name' => $this->price->getName(),
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
        // TODO: Implement getApproveData() method.
    }

    public function ready()
    {
        // TODO: Implement ready() method.
    }

    public function pay()
    {
        // TODO: Implement pay() method.
    }

    public function approve()
    {
        // TODO: Implement approve() method.
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
        return $this->price->isSubscribe() ? config('values.payment.kakao.autopay_tid') : config('values.payment.kakao.tid');
    }

    public function getCIDSecret()
    {

    }

    public function getApprovalUrl()
    {
        return "{$this->webHookURL}/payment/kakaopay/{$this->price->getID()}/paid?token={$this->member->getToken()}";
    }

    public function getCancelUrl()
    {
        return "{$this->webHookURL}/payment/kakaopay/{$this->price->getID()}/cancelled?token={$this->member->getToken()}";
    }

    public function getFailUrl()
    {
        return "{$this->webHookURL}/payment/kakaopay/{$this->price->getID()}/failed?token={$this->member->getToken()}";
    }

    public function getPayload()
    {
        $service = new StringCodeService(64);
        $model = new MembershipPaymentModel();
        return $service->createUniqueCode($model);
    }
}
