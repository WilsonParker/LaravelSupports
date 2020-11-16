<?php


namespace LaravelSupports\Libraries\Pay;


use App\Models\Membership\MembershipPaymentModel;
use App\Models\Payments\PaymentModuleModel;
use App\Services\Membership\MembershipService;
use LaravelSupports\Libraries\Coupon\CouponService;
use LaravelSupports\Libraries\Coupon\Exceptions\NotMetConditionException;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Contracts\MembershipPayment;
use LaravelSupports\Libraries\Pay\Delivery\DeliveryService;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use Throwable;

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

    /**
     * payment type
     *
     * @var string
     * @author  dew9163
     * @added   2020/06/15
     * @updated 2020/06/15
     * @example
     * kakao_pay | nice_pay
     */
    private $type;
    private $paymentModule;
    private $services = [
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

    public static function createServiceWithPayment(MembershipPayment $payment)
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

    /**
     * 결제 준비를 합니다
     *
     * *data
     * self::KEY_RECOMMEND_CODE : 'recommend_code'
     *
     * @return mixed
     * @throws Throwable
     * @author  dew9163
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
     * @throws Throwable
     * @author  dew9163
     * @added   2020/06/25
     * @updated 2020/06/25
     * @updated 2020/07/21
     * use coupon after paid
     * @updated 2020/07/22
     * plus coupon used count after paid
     * @updated 2020/07/27
     * @updated 2020/07/29
     * add coupon use count after use result is success
     */
    public function approve()
    {
        $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, $this->coupon, $this->data);
        $result = $services->approve();
        $this->bindResponseApprove($this->payment, $result);

        $membershipService = new MembershipService($this->member);

        // 결제 완료 event
        event(new MembershipPaymentCompletedEvent($this->payment, $this->member));

        // Membership 추가
        $membershipService->addMembershipWithPayment($this->payment, $this->member);

        // 추천인 코드를 입력했을 경우 추천인 등록 혜택을 제공 합니다
        $recommendCode = $this->payment->getRecommendCode();
        if (isset($recommendCode)) {
            $recommendService = new RecommendedMemberService($this->member);
            $recommendService->recommend($recommendCode, $this->price, true);
        }

        // 쿠폰 사용 처리를 합니다
        $couponService = new CouponService($this->coupon, $this->member);
        // 쿠폰 사용 횟수 증가
        if ($couponService->useCoupon($this->price, true)) {
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
     * @throws Throwable
     * @author  dew9163
     * @added   2020/07/01
     * @updated 2020/07/01
     * @updated 2020/07/22
     */
    public function subscribe()
    {
        $payment = $this->data[self::KEY_PAYMENT];
        $service = new DeliveryService();
        $options = [
            // 배송비 설정
            'delivery_cost' => $service->getDeliveryCost($this->member->address),
        ];

        /**
         * "6개월 3천원 할인" 같은 기능을 적용할 때
         * 해당 쿠폰이 아직 사용 가능한지 확인 후 할인을 적용시킬지 미정
         * 우선 한번 할인 받은 금액 만큼 할인 하도록 함
         *
         * @author  dew9163
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
        // 결제 완료 event
        event(new MembershipPaymentCompletedEvent($this->payment, $this->member));
        // Membership 추가
        $membershipService->addMembershipWithPayment($this->payment, $this->member);
        // 쿠폰 사용 횟수 증가
        $paymentModel->setStatus(Payment::STATUS_PAID);
        $paymentModel->addCouponUsedCount();
        $paymentModel->save();

        return $result->getResult();
    }

    public function payWithCoupon()
    {
        $options = [
            'status' => 'paid',
            'description' => $this->coupon->name,
            'ref_payment_module_code' => $this->type,
            'ref_membership_price_code' => $this->price->code,
            'ref_member_id' => $this->member->id,
            'coupon_used_count' => 1,
            'coupon_benefit_count' => $this->coupon->getCouponBenefitCount(),
        ];
        return MembershipPaymentModel::createModel($this->paymentModule, $this->price, $this->member, $options);
    }

    /**
     * $type 에 따른 Pay Service 제공
     *
     * @param
     * @return mixed|null
     * @author  dew9163
     * @added   2020/06/15
     * @updated 2020/06/15
     */
    public function getPayService()
    {
        return isset($this->services[$this->type]) ? $this->services[$this->type] : null;
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

}
