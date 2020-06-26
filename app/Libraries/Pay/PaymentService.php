<?php


namespace LaravelSupports\Libraries\Pay;


use App\Events\MembershipPaymentCompletedEvent;
use App\Renewal\Models\Membership\MembershipPaymentModel;
use App\Renewal\Models\Membership\PaymentModuleModel;
use LaravelSupports\Libraries\Coupon\CouponService;
use LaravelSupports\Libraries\Coupon\Exceptions\NotMetConditionException;
use App\Services\Delivery\DeliveryService;
use App\Services\Membership\MembershipService;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Contracts\MembershipPayment;
use LaravelSupports\Libraries\Pay\Common\Contracts\Payment;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use App\Services\RecommendUser\RecommendedMemberService;

class PaymentService
{
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
     * @return mixed
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/17
     * @updated 2020/06/17
     */
    public function ready()
    {
        $couponService = new CouponService($this->coupon, $this->member);
        $isUsable = $couponService->isUsable($this->price, true);
        $service = new DeliveryService();
        $options = [
            'ref_payment_module_code' => $this->paymentModule->code,
            'ref_recommend_code' => $this->data['recommend_code'],
            // 배송비 설정
            'delivery_cost' => $service->getDeliveryCost($this->member->address),
        ];
        if ($isUsable) {
            $result = $couponService->provideBenefit($this->price);
            $options = array_merge($options, [
                'sale_amount' => $result['sale_amount'],
                'coupon_benefit_count' => $result['benefit_count'],
                'ref_coupon_code' => $this->coupon->code,
                'coupon_used_count' => 1,
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
     * @author  dew9163
     * @added   2020/06/25
     * @updated 2020/06/25
     */
    public function approve()
    {
        $couponService = new CouponService($this->coupon, $this->member);
        $couponService->useCoupon($this->price, true);

        // 추천인 코드를 입력했을 경우 추천인 등록 혜택을 제공 합니다
        $recommendCode = $this->payment->getRecommendCode();
        if(isset($recommendCode)) {
            $recommendService = new RecommendedMemberService($this->member);
            $recommendService->recommend($recommendCode, $this->price, true);
        }

        $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, $this->coupon, $this->data);
//        $result = $services->approve();
//        $this->bindResponseApprove($this->payment, $result);

        $membershipService = new MembershipService($this->member);

        // 결제 완료 event
        event(new MembershipPaymentCompletedEvent($this->payment, $this->member));

        // Membership 추가
        $membershipService->addMembershipWithPayment($this->payment, $this->member);

//        return $result->getResult();
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
    }

}
