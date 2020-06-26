<?php


namespace LaravelSupports\Libraries\Pay\ImPort;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractPayService;
use LaravelSupports\Libraries\Pay\ImPort\Response\ImPortResponseAgainObject;
use LaravelSupports\Libraries\Pay\ImPort\Response\ImPortResponseOnTimeObject;
use App\Supports\DataObjects\ArrayHelper;
use Illuminate\Support\Str;

/**
 * 플러스 결제
 * constructor 에서 $data 로 card 정보를 받아야 합니다
 *
 * @author  dew9163
 * @added   2020/06/17
 * @updated 2020/06/17
 */
class ImPortPay extends AbstractPayService
{

    protected $webHookURL = 'http://test.api2.flybook.kr/v3/membership/pay/callback';
    protected $host = 'https://api.iamport.kr';
    protected $tokenURL = '/users/getToken';

    /**
     * @inheritDoc
     */
    protected function getHeaders()
    {
        return [];
    }

    public function getApproveData()
    {
        return [
            'pg' => $this->getPG(),
            'customer_uid' => $this->getCustomUID(),
            'merchant_uid' => $this->getMerchantUID(),
            'amount' => $this->getTotalAmount(),
            'name' => $this->payment->description,
            'buyer_email' => $this->member->email,
            'buyer_name' => $this->member->realname,
            'buyer_tel' => $this->member->phone,
            'buyer_postcode' => $this->member->postcode,
            'buyer_addr' => $this->member->address . ' ' . $this->member->address_detail,
            'card_number' => $this->data['card_number'],
            'expiry' => '20' . $this->data['card_year'] . '-' . $this->data['card_month'],
            'birth' => $this->data['card_certification_number'],
            'pwd_2digit' => $this->data['card_password'],
        ];
    }

    public function getSubscribeData()
    {
        return [
            'customer_uid' => $this->getCustomUID(),
            'merchant_uid' => $this->getMerchantUID(),
            'amount' => $this->getTotalAmount(),
            'name' => $this->payment->description,
            'buyer_email' => $this->member->email,
            'buyer_name' => $this->member->realname,
            'buyer_tel' => $this->member->phone,
            'buyer_postcode' => $this->member->postcode,
            'buyer_addr' => $this->member->address . ' ' . $this->member->address_detail,
        ];
    }


    /**
     * PG 사 정보
     * {PG사}.{PG상점아이디}
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/17
     * @updated 2020/06/17
     */
    protected function getPG()
    {
        return 'nice.flybook04m';
    }

    /**
     * 고객 고유 번호
     * sid
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/17
     * @updated 2020/06/17
     */
    protected function getCustomUID()
    {
        $model = $this->data['model'];
        return $model->isCustomIDSaved() ? $model->getCustomID() : Str::random(32) . ':member_' . $this->member->id;
    }

    /**
     * 가맹점 거래 고유 번호
     * uid
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/06/25
     * 고유할 수 있도록 Str 추가
     */
    protected function getMerchantUID()
    {
        return "membership_" . $this->payment->getID() . Str::random(6);
    }

    public function ready()
    {
        return new ImPortResponseOnTimeObject();
    }

    public function approve()
    {
        $model = ArrayHelper::getValueOfKeyIfExist($this->data, 'model');
        if (isset($model) && $model->isCustomIDSaved()) {
            return $this->subscription();
        } else {
            $token = $this->getAccessToken();
            $result = $this->call("/subscribe/payments/onetime?_token=$token", $this->getApproveData());
            $obj = new ImPortResponseOnTimeObject();
            $obj->bindStd($result);

            // sid 저장
            $model->saveCustomID($obj->getSID());
            $model->setCardName($obj->getCardName());
            $model->save();
            return $obj;
        }
    }

    public function subscription()
    {
        $token = $this->getAccessToken();
        $result = $this->call("/subscribe/payments/again?_token=$token", $this->getSubscribeData());
        $obj = new ImPortResponseAgainObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function cancel()
    {
    }

    public function order()
    {
    }

    public function inactive()
    {
    }

    public function paymentComplete($result)
    {
    }

    public function getAdminKey()
    {
    }

    public function getCID()
    {
    }

    public function getCIDSecret()
    {
    }

    protected function getCallbackUrl($url)
    {
        return "{$this->webHookURL}{$url}?type=nice_pay&payload={$this->getPayload()}";
    }

    public function getAccessToken()
    {
        $result = $this->call($this->tokenURL, $this->getTokenFields());
        return $result->response->access_token;
    }

    protected function getTokenFields()
    {
        return [
            'imp_key' => config('values.payment.import.key'),
            'imp_secret' => config('values.payment.import.secret'),
        ];
    }

}
