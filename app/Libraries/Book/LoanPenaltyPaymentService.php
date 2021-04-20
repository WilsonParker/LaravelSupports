<?php


namespace LaravelSupports\Libraries\Book;


use FlyBookModels\Books\LoanPenaltyPaymentModel;
use FlyBookModels\Payments\PaymentModuleModel;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Exception\CardException;
use LaravelSupports\Libraries\Pay\Common\Exception\PaymentException;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use LaravelSupports\Libraries\Supports\Date\DateHelper;
use LaravelSupports\Libraries\Supports\Objects\HasDataWithDefaultTrait;
use LaravelSupports\Libraries\Supports\String\Traits\ConvertStringTrait;

class LoanPenaltyPaymentService
{
    use ConvertStringTrait, HasDataWithDefaultTrait;

    protected $member;
    protected $data;
    protected $readyData;
    protected $type;
    protected $price;
    protected $payment;
    protected $penaltyPayment;
    protected $goods;
    protected $paymentModule;
//    protected $webHookURL = 'https://api2.flybook.kr/v3/book/penalty/payment/callback';
    protected $webHookURL = 'http://test.api2.flybook.kr/v3/book/penalty/payment/callback';
    protected $services = [
        'kakao_pay' => KakaoPay::class,
        'nice_pay' => ImPortPay::class,
    ];

    /**
     * LoanPaymentService constructor.
     *
     * @param $member
     */
    public function __construct($member = null, $data = [])
    {
        $this->member = $member;
        $this->data = $data;

        if (isset($data['type'])) {
            $this->type = $data['type'];
            $this->paymentModule = PaymentModuleModel::getModel($this->type);
        }
    }

    public static function createServiceWithPayment(LoanPenaltyPaymentModel $payment)
    {
        $parentPayment = $payment->parentPayment;
        $service = new self(
            $parentPayment->getMemberModel(),
            ['type' => $payment->getType()]
        );
        $service->payment = $parentPayment;
        $service->penaltyPayment = $payment;
        return $service;
    }

    public function approve()
    {
        $penaltyPayment = $this->penaltyPayment;
        if ($penaltyPayment->pay_amount > 0) {

            $this->throwCardInformation();
            throw_if($penaltyPayment->pay_amount < 100, new PaymentException('최소 결제 가능 금액은 100원 입니다.'));

            $services = new $this->services[$this->paymentModule->code]($this->member, $penaltyPayment, null, $this->readyData);

            $result = $services->approve();
            $this->bindResponseApprove($penaltyPayment, $result);

            return $result->getResult();
        }
    }

    public function subscribe()
    {
        $penaltyPayment = $this->payment->getPenaltyPayment();
        if ($penaltyPayment->pay_amount > 0) {

            $this->throwCardInformation();
            throw_if($penaltyPayment->pay_amount < 100, new PaymentException('최소 결제 가능 금액은 100원 입니다.'));

            $services = new $this->services[$this->paymentModule->code]($this->member, $penaltyPayment, null, $this->readyData);

            $result = $services->subscription();
            $this->bindResponseApprove($penaltyPayment, $result);

            $penaltyPayment->setStatus('paid');
            $penaltyPayment->save();

            return $result->getResult();
        }
    }

    public function getPaidResult($paymentModel)
    {
        $dateHelper = new DateHelper();

        return [
            'loan_payment_id' => $paymentModel->ref_loan_payment_id,
            'payment_id' => $paymentModel->id,
            'title' => $paymentModel->description,
            'order_no' => $paymentModel->order_no,
            'pay_amount' => $paymentModel->pay_amount,
            'created_at' => $dateHelper->formatDate($paymentModel->created_at),
            'paid_at' => $dateHelper->formatDate($paymentModel->paid_at),
        ];
    }

    protected function bindResponseApprove(LoanPenaltyPaymentModel $payment, AbstractResponseObject $result)
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

    /**
     * 결제 페이지에 필요한 정보를 제공합니다.
     *
     * @param $data
     * @return array
     * @author  seul
     * @added   2021-01-28
     * @updated 2021-01-28
     */
    public function getPaymentReadyData($payment)
    {
        $this->payment = $payment;
        $penaltyPayment = LoanPenaltyPaymentModel::where([
            'ref_loan_payment_id' => $payment->id,
            'status' => 'ready',
        ])->first();

        throw_if(!$penaltyPayment, new PaymentException('결제할 수 없는 주문입니다.'));

        $goods = [
            'overdue' => [
                'total_price' => 0,
            ],
            'damaged' => [
                'total_price' => 0,
            ],
            'delivery' => [
                'total_price' => 0,
            ],
            'etc' => [
                'total_price' => 0,
            ],
        ];

        $totalPrice = 0;
        foreach ($goods as $key => $good) {
            $goods[$key]['total_price'] = $penaltyPayment->goods()->where([
                'type' => $key,
                'status' => 'ready',
            ])->sum('total_price');

            $totalPrice += $goods[$key]['total_price'];
        }

        $goods = array_merge($goods, ['total_price' => $totalPrice]);

        $this->readyData = [
            'payment' => [
                'id' => $payment->id,
                'description' => $payment->description,
            ],
            'user' => $this->getUserData(),
            'goods' => $goods,
        ];


        return $this->readyData;


        return $this->readyData;
    }

    /**
     * 주문자 정보를 제공합니다.
     *
     * @return array
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getUserData()
    {
        return [
            'name' => $this->getName(),
            'contact' => $this->getContact(),
            'point' => $this->member->getUsablePoint(),
            'post_code' => $this->getPostCode(),
            'address' => $this->getAddress(),
            'detail_address' => $this->getAddressDetail(),
            'door_password' => $this->getAddressPassword(),
            'message' => $this->getAddressMessage(),
        ];
    }

    /**
     * 결제 준비를 합니다.
     *
     * @param
     * @return
     * @throws PaymentException
     * @author  seul
     * @added   2020-08-25
     * @updated 2020-08-25
     */
    public function ready()
    {
        $penaltyPayment = $this->bindPenaltyPayment();

        if ($penaltyPayment->pay_amount > 0) {
            $penaltyPayment->aid = null;
            $penaltyPayment->tid = null;
            $penaltyPayment->cid = null;
            $penaltyPayment->sid = null;
            $penaltyPayment->token = null;
            $penaltyPayment->save();

            $this->throwCardInformation();

            $services = new $this->services[$this->paymentModule->code]($this->member, $penaltyPayment, null, $this->readyData);

            $services->setWebHookURL($this->webHookURL);

            $result = $services->ready();
            $this->bindResponseReady($penaltyPayment, $result);
            return $result->getResult();
        } else if (isset($this->readyData['type']) && $this->readyData['type'] == 'kakao_pay') {
            throw new PaymentException();
        }
    }

    public function bindPenaltyPayment()
    {
        $options = [
            'description' => $this->getDescription(),
            'use_point' => $this->data['use_point'],
        ];

        $paymentModel = $this->payment;
        $penaltyPayment = LoanPenaltyPaymentModel::where([
            'ref_loan_payment_id' => $paymentModel->id,
            'status' => 'ready',
        ])->first();
        $totalPrice = $penaltyPayment->getTotalPriceWhereInGoodsStatus(['ready']);

        if ($this->paymentModule) {
            $penaltyPayment->ref_payment_module_code = $this->paymentModule->code;
        }

        $penaltyPayment->use_point = $this->data['use_point'];
        $penaltyPayment->pay_amount = $totalPrice - $this->data['use_point'];
        $penaltyPayment->save();

        return $penaltyPayment;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return isset($this->data->user_name) ? $this->data->user_name : $this->member->getMemberName();
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return isset($this->data->user_contact) ? $this->data->user_contact : $this->member->phone;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return isset($this->data->delivery_post_code) ? $this->data->delivery_post_code : $this->member->postcode;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return isset($this->data->delivery_address) ? $this->data->delivery_address : $this->member->address;
    }

    /**
     * @return mixed
     */
    public function getAddressDetail()
    {
        return isset($this->data->delivery_detail_address) ? $this->data->delivery_detail_address : $this->member->address_detail;
    }

    /**
     * @return mixed
     */
    public function getAddressPassword()
    {
        return isset($this->data->door_password) ? decrypt($this->data->door_password) : '';
    }

    /**
     * @return mixed
     */
    public function getAddressMessage()
    {
        return isset($this->data->address_message) ? decrypt($this->data->address_message) : '';
    }

    /**
     * 주문 생성 시 description을 제공합니다.
     *
     * @return mixed|string
     * @author  seul
     * @added   2020-08-26
     * @updated 2020-08-26
     */
    protected function getDescription()
    {
        return $this->payment->description;
    }

    public function getPaymentData($payment)
    {
        $this->getPaymentReadyData($payment);

        $this->readyData = array_merge($this->readyData, $this->data);

        return $this->readyData;
    }

    protected function bindResponseReady(LoanPenaltyPaymentModel $payment, AbstractResponseObject $result)
    {
        $payment->setTID($result->getTID());
        $payment->save();
        return $payment;
    }

    /**
     * @return mixed
     */
    public function getGoods()
    {
        return $this->goods;
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
     * 카드정보 exception 처리
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-09-25
     * @updated 2020-09-25
     */
    protected function throwCardInformation()
    {
        if ($this->readyData != null && $this->readyData['type'] == 'nice_pay' && (isset($this->readyData['card']) == false || $this->readyData['card'] == null)) {
            $cardNumber = isset($this->readyData['card_number']) ? $this->readyData['card_number'] : null;
            $cardPassword = isset($this->readyData['card_password']) ? $this->readyData['card_password'] : null;
            $cardBirth = isset($this->readyData['card_month']) ? $this->readyData['card_month'] : null;
            $cardExpiry = isset($this->readyData['card_year']) ? $this->readyData['card_year'] : null;

            throw_if(is_null($cardNumber) || is_null($cardPassword) || is_null($cardBirth) || is_null($cardExpiry), new CardException());
        }
    }
}
