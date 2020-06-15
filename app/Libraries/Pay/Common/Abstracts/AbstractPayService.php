<?php


namespace LaravelSupports\Libraries\Pay\Common\Abstracts;


use GuzzleHttp\Client;
use LaravelSupports\Libraries\Coupon\Contracts\Coupon;
use LaravelSupports\Libraries\Coupon\CouponService;
use LaravelSupports\Libraries\Pay\Common\Contracts\Member;
use LaravelSupports\Libraries\Pay\Common\Contracts\Payment;
use LaravelSupports\Libraries\Pay\Common\Contracts\Price;

abstract class AbstractPayService
{

    protected string $host;
    protected string $webHookURL;
    protected Member $member;
//    protected Price $price;
    protected ?Coupon $coupon;
    protected ?Payment $payment;

    /**
     * AbstractPayService constructor.
     *
     * @param Member $member
     * @param Price $payment
     * @param Coupon|null $coupon
     */
    public function __construct(Member $member, Payment $payment, Coupon $coupon = null)
    {
        $this->member = $member;
        $this->payment = $payment;
        $this->setCoupon($coupon);
        $this->init();
    }

    /**
     * api 를 호출 합니다
     *
     * @param $endpoint
     * @param $data
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author  dew9163
     * @added   2020/06/15
     * @updated 2020/06/15
     */
    protected function call($endpoint, $data)
    {
        $response = $this->buildRequest(
            "POST",
            $this->host . $endpoint,
            $data
        );
        return json_decode($response->getBody());
    }

    /**
     * api 를 호출하기 위한 response 를 생성 합니다
     *
     * @param $method
     * @param $url
     * @param array $params
     * @param null $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author  dew9163
     * @added   2020/06/15
     * @updated 2020/06/15
     */
    protected function buildRequest($method, $url, $params = [], $options = null)
    {
        $client = new Client();
        $response = $client->request($method, $url, [
            'headers' => $this->getHeaders(),
            'form_params' => $params
        ]);
        return $response;
    }

    /**
     * api 를 호출하기 위해 필요한 head 정보를 제공 합니다
     *
     * @return
     * @author  dew9163
     * @added   2020/06/15
     * @updated 2020/06/15
     */
    abstract protected function getHeaders();

    /**
     * ready 결제 준비에 필요한 데이터를 제공 합니다
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getReadyData();

    /**
     * 결제에 필요한 데이터를 제공 합니다
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getPayData();

    /**
     * approve 결제 승인에 필요한 데이터를 제공 합니다
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getApproveData();

    /**
     * 결제 준비를 합니다
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function ready();

    /**
     * 결제 승인을 합니다
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function approve();

    abstract public function subscription();

    abstract public function cancel();

    abstract public function order();

    abstract public function inactive();

    protected function init()
    {

    }

    /**
     * 결제가 완료 되었을 때 실행 합니다
     *
     * @param $result
     * @return
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function paymentComplete($result);

    abstract public function getAdminKey();

    /**
     * kakao
     * 가맹점 코드, 10자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getCID();

    /**
     * kakao
     * 가맹점 코드 인증키, 24자, 숫자와 영문 소문자 조합
     * Required X
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getCIDSecret();

    /**
     * kakao
     * 가맹점 주문번호, 최대 100자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getPartnerOrderID()
    {
        return $this->payment->getID();
    }

    /**
     * kakao
     * 가맹점 회원 id, 최대 100자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getPartnerUserID()
    {
        return $this->member->getID();
    }

    /**
     * kakao
     * 상품명, 최대 100자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    function getItemName()
    {
        return $this->payment->getName();
    }

    /**
     * kakao
     * 상품코드, 최대 100자
     * Required X
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getItemCode()
    {
        return '';
    }

    /**
     * kakao
     * 상품 수량
     * Required O
     *
     * @return int
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getQuantity()
    {
        return 1;
    }

    /**
     * kakao
     * 상품 총액
     * Required O
     *
     * @return float|int
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getTotalAmount()
    {
        return $this->payment->getPayAmount() * $this->getQuantity();
    }

    /**
     * kakao
     * 상품 비과세 금액
     * Required O
     *
     * @return int
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getTaxFreeAmount()
    {
        return 0;
    }

    /**
     * kakao
     * 상품 부가세 금액
     * 값을 보내지 않을 경우 다음과 같이 VAT 자동 계산
     * (상품총액 - 상품 비과세 금액)/11 : 소숫점 이하 반올림
     * Required X
     *
     * @return int
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getVatAmount()
    {
        return 0;
    }

    /**
     * kakao
     * 결제 성공 시 redirect url, 최대 255자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getApprovalUrl();

    /**
     * kakao
     * 결제 취소 시 redirect url, 최대 255자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getCancelUrl();

    /**
     * kakao
     * 결제 실패 시 redirect url, 최대 255자
     * Required O
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    abstract public function getFailUrl();

    /**
     * kakao
     * 결제 수단으로써 사용 허가할 카드사 목록, 지정하지 않으면 모든 카드사 허용
     * 현재 SHINHAN, KB, HYUNDAI, LOTTE, SAMSUNG, NH, BC, HANA, CITI, KAKAOBANK, KAKAOPAY, WOORI, GWANGJU, SUHYUP, SHINHYUP, JEONBUK, JEJU, SC 지원
     * ex) [“HANA”, “BC”]
     * Required X
     *
     * @return array
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getAvailableCards()
    {
        return null;
    }

    /**
     * kakao
     * 사용 허가할 결제 수단, 지정하지 않으면 모든 결제 수단 허용
     * CARD 또는 MONEY 중 하나
     * Required X
     *
     * @return string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getPaymentMethodType()
    {
        return 'card';
    }

    /**
     * kakao
     * 카드 할부개월, 0~12
     * Required X
     *
     * @return int
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public function getInstallMonth()
    {
        return 0;
    }

    /**
     * kakao
     * 결제 화면에 보여줄 사용자 정의 문구, 카카오페이와 사전 협의 필요
     * ex) iOS에서 사용자 인증 완료 후 가맹점 앱으로 자동 전환하는 방법(iOS만 예외 처리, 안드로이드 동작 안 함)
     * - 다음과 같이 return_custom_url key 정보에 앱스킴을 넣어서 전송
     * "return_custom_url":"kakaotalk://"
     * Required X
     *
     * @return
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    function getCustomJson()
    {
        return '';
    }

    /**
     * 결제 요청에 필요한 payload 값을 제공 합니다
     *
     * @return
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    abstract public function getPayload();

    /**
     * 등록된 coupon code 를 제공 합니다
     *
     * @return null
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    public function getCouponCode()
    {
        if (isset($this->coupon)) {
            $couponService = new CouponService($this->coupon, $this->member);
            return $couponService->getCode($this->payment->price);
        } else {
            return null;
        }
    }

    /**
     * @param Coupon|null $coupon
     */
    public function setCoupon(?Coupon $coupon): void
    {
        $this->coupon = $coupon;
    }

    /**
     * @param Payment|null $payment
     */
    public function setPayment(?Payment $payment): void
    {
        $this->payment = $payment;
    }

}
