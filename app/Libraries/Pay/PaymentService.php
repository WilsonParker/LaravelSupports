<?php


namespace LaravelSupports\Libraries\Pay;


use App\Events\Membership\MembershipPaymentCompletedEvent;
use App\Services\Membership\MembershipService;
use FlyBookModels\Membership\MembershipPaymentModel;
use FlyBookModels\Payments\PaymentModuleModel;
use LaravelSupports\Libraries\Coupon\CouponService;
use LaravelSupports\Libraries\Coupon\Exceptions\NotMetConditionException;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Contracts\MembershipPayment;
use LaravelSupports\Libraries\Pay\Common\Contracts\Payment;
use LaravelSupports\Libraries\Pay\Delivery\DeliveryService;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use LaravelSupports\Libraries\RecommendUser\RecommendedMemberService;

/**
 *
 * @author  WilsonParker
 * @added   2021/03/08
 * @updated 2021/03/08
 */
class PaymentService
{
    const KEY_PAYMENT = 'payment';
    const KEY_RECOMMEND_CODE = 'recommend_code';
    const KEY_CARD_MODEL = 'model';

    protected $member;
    protected $price;
    protected $coupon;
    protected $payment;
    protected $data;

    protected string $changeWebhookURL = 'https://api2.flybook.kr/v3/membership/change/callback';

    /**
     * payment type
     *
     * @var string
     * @author  WilsonParker
     * @added   2020/06/15
     * @updated 2020/06/15
     * @example
     * kakao_pay | nice_pay
     */
    private string $type;
    private PaymentModuleModel $paymentModule;
    private array $services = [
        'kakao_pay' => KakaoPay::class,
        'nice_pay' => ImPortPay::class,
    ];

    /**
     * PaymentService constructor.
     *
     * @param string $type
     * @param $member
     * @param $price
     * @param $coupon
     * @param null $data
     */
    public function __construct($type, $member, $price, $coupon, $data = null)
    {
        $this->member = $member;
        $this->price = $price;
        $this->coupon = $coupon;
        $this->data = $data;
        $this->type = $type;
        $this->paymentModule = PaymentModuleModel::getModel($type);
    }

    /**
     * 결제 준비를 합니다
     *
     * *data
     * self::KEY_RECOMMEND_CODE : 'recommend_code'
     *
     * @return mixed
     * @throws \Throwable
     * @author  WilsonParker
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/07/01
     */
    public function ready()
    {
        $couponService = new CouponService($this->coupon, $this->member);
        $isUsable = $couponService->isUsable($this->price, true);
        $service = new DeliveryService();
        $options = [
            'ref_payment_module_code' => $this->paymentModule->code,
            'ref_recommend_code' => $this->data[self::KEY_RECOMMEND_CODE],
            // 배송비 설정
            'delivery_cost' => $service->getDeliveryCost($this->member->address),
        ];
        if ($isUsable) {
            $result = $couponService->provideBenefit($this->price);
            $options = array_merge($options, [
                'sale_amount' => $result['sale_amount'],
                'coupon_benefit_count' => $result['benefit_count'],
                'ref_coupon_code' => $this->coupon->code,
                // 'coupon_used_count' => 1,
            ]);
        } else {
            throw_if(isset($this->coupon), new NotMetConditionException());
        }
        $paymentModel = MembershipPaymentModel::createModel($this->paymentModule, $this->price, $this->member, $options);
        $this->payment = $paymentModel;
        $services = new $this->services[$this->paymentModule->code]($this->member, $paymentModel, $this->coupon, $this->data);
        $result = $services->ready();
        $this->bindResponseReady($paymentModel, $result);
        return $result->getResult();
    }

    /**
     * 결제 건을 승인 합니다
     *
     * @return mixed
     * @throws \Throwable
     * @author  WilsonParker
     * @added   2020/06/25
     * @updated 2020/06/25
     * @updated 2020/07/21
     * use coupon after paid
     * @updated 2020/07/22
     * plus coupon used count after paid
     * @updated 2020/07/27
     * @updated 2020/07/29
     * add coupon use count after use result is success
     * @updated 2020/08/21
     * run recommend, coupon, add membership .. before payment
     */
    public function approve()
    {
        $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, $this->coupon, $this->data);
        $membershipService = new MembershipService($this->member);

        // 추천인 코드를 입력했을 경우 추천인 등록 혜택을 제공 합니다
        $recommendCode = $this->payment->getRecommendCode();
        if (isset($recommendCode)) {
            $recommendService = new RecommendedMemberService($this->member);
            $recommendService->recommend($recommendCode, $this->price, true);
        }

        // 쿠폰 사용 처리를 합니다
        $couponService = new CouponService($this->coupon, $this->member);
        // 쿠폰 사용 가능 여부 확인
        $isUsableCoupon = $couponService->useCoupon($this->price, true);

        // Membership 추가
        $membershipService->addMembershipWithPayment($this->payment, $this->member);

        $result = $services->approve();
        $this->bindResponseApprove($this->payment, $result);

        /**
         * 정기결제 처리 결제 이후로 수정
         *
         * @author  seul
         * @added   2021-02-23
         * @updated 2021-02-23
         */
        // $membershipService->addMembershipSubscribe($this->payment, $this->member->id);
        // 결제 완료 event
        event(new MembershipPaymentCompletedEvent($this->payment, $this->member));

        /**
         * 쿠폰 사용 여부를 위에서 확인 후
         * 결제 후에 쿠폰 사용 처리
         *
         * @author  WilsonParker
         * @added   2020/08/26
         * @updated 2020/08/26
         */
        if ($isUsableCoupon) {
            $this->payment->addCouponUsedCount();
            $this->payment->save();
        }

        return $result->getResult();
    }

    /**
     * 구독 결제를 합니다
     *
     * *data
     * self::KEY_PAYMENT : 'payment'
     *
     * nice_pay : 'model':MemberCard
     *
     * @return mixed
     * @throws \Throwable
     * @author  WilsonParker
     * @added   2020/07/01
     * @updated 2020/07/01
     * @updated 2020/07/22
     * @updated 2020/12/28
     * 쿠폰 사용이 되지 않았는데 쿠폰 사용 횟수가 증가하는 문제 처리
     */
    public function subscribe()
    {
        $payment = $this->data[self::KEY_PAYMENT];
        $service = new DeliveryService();
        /**
         * 결제수단 변경으로 인해 description 재설정 추가
         *
         * @author  seul
         * @added   2020-10-20
         * @updated 2020-10-20
         */
        $options = [
            // 배송비 설정
            'delivery_cost' => $service->getDeliveryCost($this->member->address),
            'description' => $this->price->description,
        ];

        /**
         * "6개월 3천원 할인" 같은 기능을 적용할 때
         * 해당 쿠폰이 아직 사용 가능한지 확인 후 할인을 적용시킬지 미정
         * 우선 한번 할인 받은 금액 만큼 할인 하도록 함
         *
         * @author  WilsonParker
         * @added   2020/07/22
         * @updated 2020/07/22
         */
        /*if ($payment->isCouponUsable()) {
            $options = array_merge($options, [
                // 쿠폰 사용 횟수 증가
                'coupon_used_count' => $payment->coupon_used_count + 1,
            ]);
        }*/
        $paymentModel = MembershipPaymentModel::createModelWithSelf($payment, $options);
        $this->payment = $paymentModel;
        $services = new $this->services[$this->paymentModule->code]($this->member, $paymentModel, null, $this->data);
        $result = $services->subscription();

        $this->bindResponseSubscription($paymentModel, $result);
        $membershipService = new MembershipService($this->member);
        /**
         * 자동 결제 시 카카오 알림톡 보내지 않음
         *
         * @author  WilsonParker
         * @added   2020/08/14
         * @updated 2020/08/14
         */
        // 결제 완료 event
        // event(new MembershipPaymentCompletedEvent($this->payment, $this->member));

        // Membership 추가
        $membershipService->addMembershipWithPayment($this->payment, $this->member);

        /**
         *  정기결제 업데이트 분리
         *
         * @author  seul
         * @added   2021-02-24
         * @updated 2021-02-24
         */
        // $membershipService->addMembershipSubscribe($this->payment, $this->member->id);

        // 쿠폰 사용 횟수 증가
        $paymentModel->setStatus(Payment::STATUS_PAID);
        if ($paymentModel->isRemainedBenefit()) {
            $paymentModel->addCouponUsedCount();
        }
        $paymentModel->save();

        return $result->getResult();
    }

    /**
     * Membership 결제수단을 업데이트할 준비를 합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-10-19
     * @updated 2020-10-19
     */
    public function readyToUpdate()
    {
        // 결제 정보를 초기화 합니다.
        $options = [
            'aid' => null,
            'tid' => null,
            'cid' => null,
            'sid' => null,
            'uid' => null,
            'pg_tid' => null,
            'pg_provider' => null,
            'token' => null,
            'payment_type' => null,
            'status' => 'ready',
            'description' => $this->price->description . ' - 결제 수단 변경',
            'ref_payment_module_code' => $this->paymentModule->code,
            'error_message' => null,
        ];

        $paymentModel = MembershipPaymentModel::createModelWithSelf($this->payment, $options, false);

        $paymentModel->price = 0;
        $paymentModel->sale_amount = 0;
        $paymentModel->delivery_cost = 0;
        $paymentModel->pay_amount = 0;

        $this->payment = $paymentModel;
        $service = new $this->services[$this->paymentModule->code]($this->member, $paymentModel, $this->coupon, $this->data);
        $service->setWebHookURL($this->changeWebhookURL);
        $result = $service->ready();

        // 결제 금액 0원으로 변경한 부분 수정
        $paymentModel->refresh();

        $this->bindResponseReady($paymentModel, $result);
        return $result->getResult();
    }

    /**
     * Membership 결제수단을 업데이트 합니다.
     *
     * @return mixed
     * @author  seul
     * @added   2020-10-16
     * @updated 2020-10-16
     */
    public function updateSubscribe()
    {
        $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, $this->coupon, $this->data);

        $result = $services->storeSubscribeUser();
        $this->bindResponseApprove($this->payment, $result);

        $subscriber = $this->member->subscriber;
        $subscriber->ref_membership_payment_id = $this->payment->id;
        $subscriber->save();

        return $result->getResult();
    }

    public function payWithCoupon($data = [])
    {
        $options = [
            'status' => 'paid',
            'description' => $this->coupon->name,
            'ref_payment_module_code' => $this->type,
            'ref_membership_price_code' => $this->price->code,
            'ref_member_id' => $this->member->id,
            'coupon_used_count' => 1,
            'coupon_benefit_count' => $this->coupon->getCouponBenefitCount(),
            'pay_amount' => '0',
            'ref_coupon_code' => $this->coupon->code,
        ];
        $result = array_merge($options, $data);
        return MembershipPaymentModel::createModel($this->paymentModule, $this->price, $this->member, $result);
    }

    /**
     * $type 에 따른 Pay Service 제공
     *
     * @param
     * @return mixed|null
     * @author  WilsonParker
     * @added   2020/06/15
     * @updated 2020/06/15
     */
    public function getPayService()
    {
        return isset($this->services[$this->type]) ? $this->services[$this->type] : null;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    private function bindResponseReady(MembershipPayment $payment, AbstractResponseObject $result)
    {
        $payment->setTID($result->getTID());
        $payment->save();
        return $payment;
    }

    private function bindResponseApprove(MembershipPayment $payment, AbstractResponseObject $result)
    {
        $payment->setAID($result->getAID());
        $payment->setTID($result->getTID());
        $payment->setCID($result->getCID());
        $payment->setSID($result->getSID());
        $payment->setUID($result->getUID());
        $payment->setPgTid($result->getPgTid());
        $payment->setPgProvider($result->getPgProvider());
        $payment->setPaymentType($result->getPaymentMethodType());
        $payment->save();
        return $payment;
    }

    private function bindResponseSubscription(MembershipPayment $payment, AbstractResponseObject $result)
    {
        return $this->bindResponseApprove($payment, $result);
    }

    public static function createServiceWithPayment(MembershipPayment $payment): self
    {
        $service = new self(
            $payment->getType(),
            $payment->getMemberModel(),
            $payment->getPriceModel(),
            $payment->getCouponModel()
        );
        $service->payment = $payment;
        return $service;
    }
}
