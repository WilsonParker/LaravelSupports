<?php


namespace LaravelSupports\Libraries\Pay\Kakao;


use App\Models\Membership\MembershipPaymentModel;
use App\Models\Payments\PaymentModuleModel;
use LaravelSupports\Libraries\Codes\StringCodeService;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractPayService;

class KakaoPay extends AbstractPayService
{
    protected string $webHookURL = 'http://test.api2.flybook.kr';
    protected string $host = 'https://kapi.kakao.com';
    private $payload;

    protected function init()
    {
        $service = new StringCodeService(64);
        $this->payload = $service->createCode();
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

    public function getPayData()
    {
        /*return [
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

        ];*/
    }

    public function getApproveData()
    {

    }

    public function ready()
    {
        $paymentModuleModel = PaymentModuleModel::getModel('kakao_pay');
        $options = [
            'sale_amount' => 3000
        ];
        $paymentModel = MembershipPaymentModel::createModel($paymentModuleModel, $this->payment, $this->member, $options);
        $this->setPayment($paymentModel);
        $result = $this->call("/v1/payment/ready", $this->getReadyData());

        return $result;
    }

    public function approve()
    {
        return $this->call("/v1/payment/approve", []);
    }

    public function subscription()
    {
        return $this->call("/v1/payment/subscription", []);
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

    public function getApprovalUrl()
    {
        return $this->getCallbackUrl("/v3/membership/pay/paid");
    }

    public function getCancelUrl()
    {
        return $this->getCallbackUrl("/v3/membership/pay/cancelled");
    }

    public function getFailUrl()
    {
        return $this->getCallbackUrl("/v3/membership/pay/failed");
    }

    protected function getCallbackUrl($url)
    {
        return "{$this->webHookURL}{$url}?type=kakaopay&token={$this->member->getToken()}&payload={$this->getPayload()}&price={$this->getPartnerOrderID()}&coupon={$this->getCouponCode()}";
    }

    public function getPayload()
    {
        $service = new StringCodeService(64);
        $model = new MembershipPaymentModel();
        return $service->createUniqueCode($model);
    }

}
