<?php


namespace LaravelSupports\Pay\ImPort;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use LaravelSupports\Data\ArrayHelper;
use LaravelSupports\Pay\Common\Abstracts\AbstractPayService;
use LaravelSupports\Pay\ImPort\Response\ImPortResponseAgainObject;
use LaravelSupports\Pay\ImPort\Response\ImPortResponseCancelObject;
use LaravelSupports\Pay\ImPort\Response\ImPortResponseOnTimeObject;
use LaravelSupports\Pay\ImPort\Response\ImPortResponseStoreSubscribeUserObject;
use LaravelSupports\Pay\ImPort\Response\ImPortResponseSubscribeUserObject;
use Throwable;

/**
 * 플러스 결제
 * constructor 에서 $data 로 card 정보를 받아야 합니다
 *
 * @author  WilsonParker
 * @added   2020/06/17
 * @updated 2020/06/17
 */
class ImPortPay extends AbstractPayService
{

    protected string $webHookURL = 'https://api2.flybook.kr/v3/membership/pay/callback';
    protected string $host = 'https://api.iamport.kr';
    protected $tokenURL = '/users/getToken';
    private $token;

    public function ready()
    {
        return new ImPortResponseOnTimeObject();
    }

    public function approve()
    {
        $model = ArrayHelper::getValueOfKeyIfExist($this->data, 'model');
        $isNew = ArrayHelper::getValueOfKeyIfExist($this->data, 'isNew', false);
        // 무료 결제 일 경우
        if ($this->payment->getPayAmount() == 0) {
            // 저장된 카드를 사용할 경우 customUID 가 유효한지 확인
            if (!$isNew) {
                return $this->subscribeUser();
            } else {
                return $this->storeSubscribeUser();
            }
        } else if (isset($model) && !$isNew && ($model->isCustomIDSaved() || $model->isOriginCard())) {
            return $this->subscription();
        } else {
            $result = $this->call("/subscribe/payments/onetime?_token={$this->token}", $this->getApproveData());
            $obj = new ImPortResponseOnTimeObject();
            $obj->bindStd($result);

            if ($this->data['save_card']) {
                // sid 저장
                $model->saveCustomID($obj->getSID());
                $model->setCardName($obj->getCardName());
                $model->save();
            }
            return $obj;
        }
    }

    /**
     * 구독 결제 이용자 정보를 제공 합니다
     *
     * @return ImPortResponseSubscribeUserObject
     * @throws GuzzleException
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/06/26
     * @updated 2020/06/26
     */
    public function subscribeUser()
    {
        $result = $this->call("/subscribe/customers/{$this->getCustomUID()}?_token={$this->token}", [], 'GET');
        $obj = new ImPortResponseSubscribeUserObject();
        $obj->bindStd($result);
        return $obj;
    }

    /**
     * 고객 고유 번호
     * sid
     *
     * @return string
     * @author  WilsonParker
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/06/26
     * add Origin Card payment uid
     */
    protected function getCustomUID()
    {
        $model = ArrayHelper::getValueOfKeyIfExist($this->data, 'model');
        if (is_null($model)) {
            if (isset($this->payment) && !is_null($this->payment->getSID())) {
                return $this->payment->getSID();
            }
        } else {
            if ($model->isCustomIDSaved()) {
                return $model->getCustomID();
            } else if ($model->isOriginCard()) {
                return $this->member->id;
            }
        }
        return Str::random(32) . ':member_' . $this->member->id;
    }

    /**
     *
     * @return ImPortResponseStoreSubscribeUserObject
     * @throws GuzzleException
     * @throws Throwable
     * @author  WilsonParker
     * @added   2020/07/21
     * @updated 2020/07/21
     */
    public function storeSubscribeUser()
    {
        $result = $this->call("/subscribe/customers/{$this->getCustomUID()}?_token={$this->token}", $this->getStoreSubscribeData(), 'POST');
        $obj = new ImPortResponseStoreSubscribeUserObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function getStoreSubscribeData()
    {
        return [
            'customer_uid' => $this->getCustomUID(),
            'pg' => $this->getPG(),
            'card_number' => $this->data['card_number'],
            'expiry' => '20' . $this->data['card_year'] . '-' . $this->data['card_month'],
            'birth' => $this->data['card_certification_number'],
            'pwd_2digit' => $this->data['card_password'],
            'customer_name' => $this->member->realname,
            'customer_tel' => $this->member->phone,
            'customer_email' => $this->member->email,
            'customer_addr' => $this->member->address . ' ' . $this->member->address_detail,
            'customer_postcode' => $this->member->postcode,
        ];
    }

    /**
     * PG 사 정보
     * {PG사}.{PG상점아이디}
     *
     * @return string
     * @author  WilsonParker
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/07/22
     * 구독 방식 과 일반 결제 PG 설정
     */
    protected function getPG()
    {
        return $this->payment->membershipPrice->isSubscribe() ? 'nice.flybook02m' : 'nice.flybook04m';
    }

    public function subscription()
    {
        $result = $this->call("/subscribe/payments/again?_token={$this->token}", $this->getSubscribeData());
        $obj = new ImPortResponseAgainObject();
        $obj->bindStd($result);
        return $obj;
    }

    public function getSubscribeData()
    {
        return [
            'customer_uid' => $this->getCustomUID(),
            'merchant_uid' => $this->getMerchantUID(),
            'amount' => $this->getTotalAmount(),
            'tax_free' => $this->getTaxFreeAmount(),
            'name' => $this->payment->description,
            'buyer_email' => $this->member->email,
            'buyer_name' => $this->member->realname,
            'buyer_tel' => $this->member->phone,
            'buyer_postcode' => $this->member->postcode,
            'buyer_addr' => $this->member->address . ' ' . $this->member->address_detail,
        ];
    }

    /**
     * 가맹점 거래 고유 번호
     * uid
     *
     * @return string
     * @author  WilsonParker
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/06/25
     * 고유할 수 있도록 Str 추가
     */
    protected function getMerchantUID()
    {
        return Str::random(16) . ":membership_" . $this->payment->getID();
    }

    public function getApproveData()
    {
        return [
            'pg' => $this->getPG(),
            'customer_uid' => $this->getCustomUID(),
            'merchant_uid' => $this->getMerchantUID(),
            'amount' => $this->getTotalAmount(),
            'tax_free' => $this->getTaxFreeAmount(),
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

    /**
     * 결제 취소
     *
     * @return ImPortResponseCancelObject
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @author  seul
     * @added   2020-08-31
     * @updated 2020-08-31
     */
    public function cancel()
    {
        $result = $this->call("/payments/cancel?_token={$this->token}", $this->getCancelData(), 'POST');
        $obj = new ImPortResponseCancelObject();
        $obj->bindStd($result);

        return $obj;
    }

    public function getCancelData()
    {
        return [
            'merchant_uid' => $this->getUID(),
            'amount' => $this->data['cancel_amount'],
            'tax_free' => isset($this->data['tax_free']) ? $this->data['tax_free'] : $this->data['cancel_amount'],
            'reason' => $this->data['reason'],
        ];
    }

    protected function getUID()
    {
        return $this->payment->getUID();
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

    protected function init()
    {
        $this->token = $this->getAccessToken();
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

    /**
     * @inheritDoc
     */
    protected function getHeaders()
    {
        return [];
    }

    protected function getCallbackUrl($url)
    {
        return "{$this->webHookURL}{$url}?type=nice_pay&payload={$this->getPayload()}";
    }

}
