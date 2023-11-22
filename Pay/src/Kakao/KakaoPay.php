<?php


namespace LaravelSupports\Pay\Kakao;


use LaravelSupports\Pay\Common\Abstracts\AbstractPayService;
use LaravelSupports\Pay\Kakao\Response\KakaoResponseApproveObject;
use LaravelSupports\Pay\Kakao\Response\KakaoResponseCancelObject;
use LaravelSupports\Pay\Kakao\Response\KakaoResponseReadyObject;

class KakaoPay extends AbstractPayService
{

    protected string $webHookURL = 'http://api.flybook.kr/membership/pay/callback';
    protected string $host = 'https://kapi.kakao.com';

    public function getOrderData(): array
    {
        return [
            'cid' => $this->getCID(),
            'tid' => $this->payment->getTID(),
        ];
    }

    public function getCID()
    {
        return $this->payment->isSubscribe() ? config('values.payment.kakao.autopay_tid') : config('values.payment.kakao.tid');
    }

    public function getTID()
    {
        return $this->payment->getTID();
    }

    public function ready()
    {
        $result = $this->call("/v1/payment/ready", $this->getReadyData());
        $obj = new KakaoResponseReadyObject();
        $obj->bindStd($result);
        return $obj;
    }

    /**
     * @author  seul
     * @updated 2020/11/19
     * 비과세금액 필수로 결제금액과 동일하게 제공되도록 수정하였습니다.
     * (카카오페이 자동발행 현금영수증 관련 요청 사항)
     */
    public function getReadyData(): array
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

    public function subscription()
    {
        $result = $this->call("/v1/payment/subscription", $this->getSubscribeData());
        $obj = new KakaoResponseApproveObject();
        $obj->bindStd($result);
        return $obj;
    }

    /**
     * add tax free amount
     *
     * @inheritDoc
     * @author  seul
     * @updated 2020-11-19
     */
    public function getSubscribeData(): array
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
            'tax_free_amount' => $this->getTaxFreeAmount(),
        ];
    }

    public function getSID()
    {
        return $this->payment->getSID();
    }

    public function cancel()
    {
        return $this->call("/v1/payment/cancel", $this->getCancelData());
        $obj = new KakaoResponseCancelObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function getCancelData(): array
    {
        return [
            'cid' => $this->getCID(),
            'tid' => $this->getTID(),
            'cancel_amount' => $this->data['cancel_amount'],
            'cancel_tax_free_amount' => isset($this->data['tax_free']) ? $this->data['tax_free'] : $this->data['cancel_amount'],
        ];
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

    public function getCIDSecret()
    {
        return null;
    }

    /**
     * 정기결제를 신규로 추가합니다.
     *
     * @return KakaoResponseApproveObject
     * @author  seul
     * @added   2020-10-16
     * @updated 2020-10-16
     */
    public function storeSubscribeUser(): KakaoResponseApproveObject
    {
        return $this->approve();
    }

    public function approve()
    {
        $result = $this->call("/v1/payment/approve", $this->getApproveData());
        $obj = new KakaoResponseApproveObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function getApproveData(): array
    {
        return [
            'cid' => $this->getCID(),
            'tid' => $this->payment->getTID(),
            'partner_order_id' => $this->getPartnerOrderID(),
            'partner_user_id' => $this->getPartnerUserID(),
            'pg_token' => $this->getToken(),
        ];
    }

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

    public function getAdminKey()
    {
        return config('values.payment.kakao.key');
    }

    protected function getCallbackUrl($url): string
    {
        return "{$this->webHookURL}{$url}?type=kakao_pay&payload={$this->getPayload()}";
    }

}
