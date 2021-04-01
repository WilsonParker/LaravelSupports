<?php


namespace LaravelSupports\Libraries\Book;


use Carbon\Carbon;
use FlyBookModels\Books\BookOrderedStoreModel;
use FlyBookModels\Books\LoanBookPaymentGoodsModel;
use FlyBookModels\Books\LoanBookPaymentModel;
use FlyBookModels\Books\LoanPenaltyPaymentGoodsModel;
use FlyBookModels\Books\LoanPenaltyPaymentModel;
use FlyBookModels\Delivery\ConfigDeliveryCostModel;
use FlyBookModels\Delivery\DeliveryModel;
use FlyBookModels\Members\MemberCardModel;
use FlyBookModels\Members\MemberPointModel;
use FlyBookModels\Offline\OfflineLoanBookModel;
use FlyBookModels\Payments\PaymentModuleModel;
use LaravelSupports\Libraries\Book\Exceptions\LocationException;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Exception\CardException;
use LaravelSupports\Libraries\Pay\Common\Exception\IsNotReturnablePaymentException;
use LaravelSupports\Libraries\Pay\Common\Exception\PaymentException;
use LaravelSupports\Libraries\Pay\Delivery\DeliveryService;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use LaravelSupports\Libraries\Supports\Date\DateHelper;
use LaravelSupports\Libraries\Supports\Objects\HasDataWithDefaultTrait;
use LaravelSupports\Libraries\Supports\String\Traits\ConvertStringTrait;

class LoanPaymentService
{
    use ConvertStringTrait, HasDataWithDefaultTrait;

    protected $member;
    protected $data;
    protected $readyData;
    protected $type;
    protected $price;
    protected $payment;
    protected $goods;
    protected $loanCost = 3000;
    protected $deliveryRuleCode = 'loan';
    protected $deliveryRuleModel;
    protected $paymentModule;
    protected $webHookURL = 'https://api2.flybook.kr/v3/book/loan/payment/callback';
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

        $this->deliveryRuleModel = ConfigDeliveryCostModel::getModelWhereCode($this->deliveryRuleCode);
    }

    public static function createServiceWithPayment(LoanBookPaymentModel $payment)
    {
        $service = new self(
            $payment->getMemberModel(),
            ['type' => $payment->getType()]
        );
        $service->payment = $payment;
        return $service;
    }

    public function approve()
    {
        if ($this->payment->pay_amount > 0) {
            $this->throwCardInformation();
            throw_if($this->payment->pay_amount < 100, new PaymentException('최소 결제 가능 금액은 100원 입니다.'));

            $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, null, $this->readyData);

            $result = $services->approve();
            $this->bindResponseApprove($this->payment, $result);

            return $result->getResult();
        }
    }

    public function getPaidResult($paymentModel)
    {
        $dateHelper = new DateHelper();

        return [
            'payment_id' => $paymentModel->id,
            'title' => $paymentModel->description,
            'order_no' => $paymentModel->order_no,
            'pay_amount' => $paymentModel->pay_amount,
            'created_at' => $dateHelper->formatDate($paymentModel->created_at),
            'paid_at' => $dateHelper->formatDate($paymentModel->paid_at),
        ];
    }

    protected function bindResponseApprove(LoanBookPaymentModel $payment, AbstractResponseObject $result)
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
     * @return mixed
     */
    public function getDeliveryRuleModel()
    {
        return $this->deliveryRuleModel;
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
    public function getPaymentReadyData()
    {
        $this->goods = $this->getGoodsBookInfo($this->data);

        $freeLoanTotalCount = (int)$this->member->getMemberBenefit('delivery_loanable_count');
        $usedLoanCount = $freeLoanTotalCount > 0 ? (int)LoanBookPaymentModel::countGoodsWhereMonth($this->member) : 0;
        $usedLoanCount = $usedLoanCount > $freeLoanTotalCount ? $freeLoanTotalCount : $usedLoanCount;
        $loanCount = count($this->goods) - ($freeLoanTotalCount - $usedLoanCount);
        $totalPrice = $loanCount > 0 ? $loanCount * $this->loanCost : 0;
        $deliveryCost = $this->deliveryRuleModel->getDefaultDeliveryCost($totalPrice);

        $service = new LoanScheduleService();
        $this->readyData = [
            'has_card' => MemberCardModel::existsModel($this->member->id),
            'loan_scheduled_delivery_date' => $service->getScheduledDeliveryDate(),
            'delivery_loanable_days' => (int)$this->member->getMemberBenefit('delivery_loanable_days'),
            'free_loan_total_count' => $freeLoanTotalCount,
            'free_loan_used_count' => $usedLoanCount,
            'loan_cost' => $this->loanCost,
            'delivery_cost' => $freeLoanTotalCount > $usedLoanCount ? 0 : $deliveryCost,
            'user' => $this->getUserData(),
            'max_quantity' => $this->getMaxQuantity(),
            'goods' => $this->goods,
        ];


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
     * 주문 최대 수량 정보를 제공합니다.
     *
     * @return array
     * @author  seul
     * @added   2021-01-28
     * @updated 2021-01-28
     */
    public function getMaxQuantity()
    {
        $cartService = new LoanCartService();
        return [
            'cart' => $cartService->getMaxCartQuantity(),
            'book' => $cartService->getMaxBookQuantity(),
            'total' => $cartService->getMaxTotalQuantity(),
        ];
    }

    /**
     * 도서 주문에 필요한 도서 정보를 제공 합니다.
     *
     * @param
     * @return array
     * @author  seul
     * @added   2021-01-28
     * @updated 2021-01-28
     */
    public function getGoodsBookInfo($data)
    {
        $bookCarts = LoanBookCartModel::getCartPartListWithBook($this->member->id, $data['carts']);

        return $bookCarts->filter(function ($item) {
            $book = $item->book;
            $stockService = new LoanStockService($book);
            $storeStock = $stockService->getBookStock();
            return $storeStock['loanable'];
        })->map(function ($item) {
            $book = $item->book;
            $service = new LoanStockService($book);
            $stockInfo = $service->getBookStock();

            return [
                'cart_id' => $item->id,
                'book_id' => $item->ref_book_id,
                'quantity' => $item->quantity,
                'title' => $book->title,
                'book_img' => $book->book_img,
                'publisher' => $book->publisher,
                'author' => $book->author,
                'sales_price' => $book->sales_price,
                'store_code' => $stockInfo['loan_store'],
            ];
        });
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
        $this->throwLocation();

        $options = [
            'description' => $this->getDescription(),
            'use_point' => $this->data['use_point'],
        ];

        $paymentModel = LoanBookPaymentModel::createModel($this->paymentModule, $this->member, $options);
        $this->createGoodsModel($paymentModel);
        $totalPrice = $paymentModel->getTotalPriceWhereInGoodsStatus(['ready']);
        $delivertCostArray = $this->getDeliveryCost($this->getAddress(), $paymentModel);

        $data = [
            'name' => $this->data['user_name'],
            'phone' => $this->data['user_contact'],
            'post_code' => $this->data['delivery_post_code'],
            'address' => $this->data['delivery_address'],
            'address_detail' => $this->data['delivery_detail_address'],
            'door_password' => isset($this->data['door_password']) && $this->data['door_password'] ? encrypt($this->data['door_password']) : null,
            'delivery_memo' => isset($this->data['delivery_memo']) ? $this->data['delivery_memo'] : null,
        ];

        $deliveryModel = DeliveryModel::createModel($data, $delivertCostArray);
        $paymentModel->goods()->get()->each(function ($item) use ($deliveryModel) {
            $item->ref_delivery_id = $deliveryModel->id;
            $item->save();
        });

        $paymentModel->setPayAmountWhereInStatus($totalPrice);
        $paymentModel->save();

        $this->setPayment($paymentModel);

        if ($paymentModel->pay_amount > 0) {
            $this->throwCardInformation();

            $services = new $this->services[$this->paymentModule->code]($this->member, $this->payment, null, $this->readyData);

            $services->setWebHookURL($this->webHookURL);

            $result = $services->ready();
            $this->bindResponseReady($paymentModel, $result);
            return $result->getResult();
        } else {
            switch ($this->paymentModule->code) {
                case 'nice_pay':
                    if (isset($this->readyData['model'])) {
                        $paymentModel->sid = $this->readyData['model']->custom_id;
                    }
                    break;
            }
        }
    }

    /**
     * 배송비를 계산하여 제공합니다.
     * 책 구매 금액이 10,000원 이상일 경우 배송비 무료, 이하일 경우 지역으로 배송비 산정
     *
     * @param $delivery_address
     * @param $totalPrice
     * @return array
     * @author  seul
     * @added   2020-08-25
     * @updated 2020-08-25
     */
    public function getDeliveryCost($delivery_address, $paymentModel)
    {
        $totalPrice = $paymentModel->getTotalPriceWhereInGoodsStatus(['ready']);

        $hasFreeGoods = $paymentModel->goods()->get()->filter(function ($item) {
            return $item->total_price == 0;
        })->isNotEmpty();

        $defaultCost = $hasFreeGoods ? 0 : $this->deliveryRuleModel->getDefaultDeliveryCost($totalPrice);

        $deliveryService = new DeliveryService();
        $addCost = $hasFreeGoods ? 0 : $deliveryService->getDeliveryCost($delivery_address);

        return [
            'default_cost' => $defaultCost,
            'add_cost' => $addCost,
            'delivery_cost' => $defaultCost + $addCost,
        ];
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
        $title = $this->shortenString($this->goods->first()['title']);
        $count = count($this->goods) - 1;
        if ($count > 0) {
            $title .= " 외 {$count}건";
        }

        return $title;
    }

    public function getPaymentData()
    {
        $this->getPaymentReadyData();

        $this->readyData = array_merge($this->readyData, $this->data);

        return $this->readyData;
    }

    protected function bindResponseReady(LoanBookPaymentModel $payment, AbstractResponseObject $result)
    {
        $payment->setTID($result->getTID());
        $payment->save();
        return $payment;
    }

    public function createGoodsModel($paymentModel)
    {
        $goods = $this->getGoods();

        $freeLoanCount = $this->readyData['free_loan_total_count'];
        $count = $this->readyData['free_loan_used_count'];
        foreach ($goods as $good) {
            $price = $freeLoanCount > $count ? 0 : $this->loanCost;
            $store = BookOrderedStoreModel::where('code', $good['store_code'])->first();
            $data = [
                'ref_payment_id' => $paymentModel->id,
                'status' => 'ready',
                'ref_book_ordered_store_id' => $store ? $store->id : null,
                'ref_book_id' => $good['book_id'],
                'quantity' => $good['quantity'],
                'price' => $price,
                'total_price' => $good['quantity'] * $price,
            ];

            LoanBookPaymentGoodsModel::create($data);

            $count++;
        }
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
        if ($this->readyData['type'] == 'nice_pay' && (isset($this->readyData['card']) == false || $this->readyData['card'] == null)) {
            $cardNumber = isset($this->readyData['card_number']) ? $this->readyData['card_number'] : null;
            $cardPassword = isset($this->readyData['card_password']) ? $this->readyData['card_password'] : null;
            $cardBirth = isset($this->readyData['card_month']) ? $this->readyData['card_month'] : null;
            $cardExpiry = isset($this->readyData['card_year']) ? $this->readyData['card_year'] : null;

            throw_if(is_null($cardNumber) || is_null($cardPassword) || is_null($cardBirth) || is_null($cardExpiry), new CardException());
        }
    }

    /**
     * 일부 지역만 대여 가능
     *
     * @param
     * @return
     * @throws \Throwable
     * @author  seul
     * @added   2021-01-29
     * @updated 2021-01-29
     * 현재 서울 지역만 가능
     */
    protected function throwLocation()
    {
        $address = mb_substr($this->data['delivery_address'], 0, 2);

        throw_if($address != '서울', new LocationException());
    }

    /**
     *
     *
     * @param
     * @return
     * @author  seul
     * @added   2021-01-29
     * @updated 2021-01-29
     */
    public function cancelAll()
    {
        $payment = $this->payment;
        $cancelPoint = $payment->getUsePoint();
        $cancelAmount = $payment->getTotalPayAmount();
        $data = [
            'cancel_amount' => $cancelAmount,
            'reason' => '고객 요청 취소',
        ];

        if ($cancelAmount > 0) {
            $services = new $this->services[$payment->ref_payment_module_code]($this->member, $payment, null, $data);
            $services->cancel();
        }

        $memo = $payment->description . ' 취소건으로 인한 포인트 환급';
        $destroyedAt = Carbon::now()->addMonth(6)->format('Y-m-d');
        MemberPointModel::createPoint($payment->getUsePoint(), $this->member->id, $memo, $destroyedAt);

        $status = 'cancelled';
        $payment->status = $status;
        $payment->cancel_amount += $cancelAmount;
        $payment->cancel_point += $cancelPoint;
        $payment->cancelled_at = now();
        $payment->save();

        $payment->goods()->each(function ($item) {
            $book = $item->book;
            $offlineStock = OfflineLoanBookModel::findStock($book->id, 26);
            if ($offlineStock) {
                if ($offlineStock->loaned_qunatity > 1) {
                    $offlineStock->decrement('loaned_quantity', 1);
                    $offlineStock->increment('remained_quantity', 1);
                }
            }
        });

        $payment->goods()->update([
            'status' => $status,
            'cancelled_at' => now(),
        ]);
    }

    public function returnAll()
    {
        $goods = $this->payment->goods()
            ->where('status', 'shipping')
            ->whereHas('history', function ($query) {
                $query->whereIn('status', ['loaned', 'overdue']);
            })
            ->get();
        $this->updateStatus($goods, 'pickup');
    }

    public function returnGoods($goodIDs)
    {
        $payment = $this->payment;
        $goods = $payment->goods()
            ->whereIn('id', $goodIDs)
            ->where('status', 'shipping')
            ->whereHas('history', function ($query) {
                $query->whereIn('status', ['loaned', 'overdue']);
            })
            ->get();

        throw_if(count($goodIDs) != count($goods), new IsNotReturnablePaymentException());

        $this->updateStatus($goods, 'pickup');
    }

    protected function updateStatus($goods, $status)
    {
        $goods->each(function ($item) use ($status) {
            $history = $item->history;
//            throw_if(!$history || !in_array($history->status, ['loaned', 'overdue']), new DeliveryNotFoundException());

            $scheduledDate = $history->scheduled_return_date->format('Y-m-d');
            $today = date('Y-m-d');
            if ($today > $scheduledDate) {
                $diff = (new \DateTime($today))->diff(new \DateTime($scheduledDate))->days;
                $this->createPenaltyPayment($item, $diff);
            }

            $history->update([
                'status' => $status,
                'return_date' => now(),
            ]);
        });
    }

    protected function createPenaltyPayment($good, $quantity = 1)
    {
        $payment = $this->payment;
        $penaltyPayment = $payment->getPenaltyPayment();
        if (!$penaltyPayment) {
            $penaltyPayment = LoanPenaltyPaymentModel::createModel($payment);
        }

        $overduePrice = 1000;
        LoanPenaltyPaymentGoodsModel::updateOrCreate(
            [
                'ref_payment_id' => $penaltyPayment->id,
                'ref_book_id' => $good->ref_book_id,
                'status' => 'ready',
                'type' => 'overdue',
            ],
            [
                'quantity' => $quantity,
                'price' => $overduePrice,
                'total_price' => $quantity * $overduePrice,
            ]);
    }

    public function storePenaltyPayment($data)
    {
        $payment = $this->payment;
        $penaltyPayment = $payment->getPenaltyPayment();
        if (!$penaltyPayment) {
            $penaltyPayment = LoanPenaltyPaymentModel::createModel($payment);
        }

        foreach ($data['book_id'] as $key => $item) {
            if ($key == 'new') {
                foreach ($item as $itemKey => $bookID) {
                    if ($bookID == '') {
                        continue;
                    }

                    $quantity = $data['quantity']['new'][$itemKey];
                    $price = $data['price']['new'][$itemKey];

                    LoanPenaltyPaymentGoodsModel::create([
                        'ref_payment_id' => $penaltyPayment->id,
                        'ref_book_id' => $bookID,
                        'status' => 'ready',
                        'type' => $data['type']['new'][$itemKey],
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_price' => $quantity * $price,
                    ]);
                }
            } else {
                $goodMdoel = LoanPenaltyPaymentGoodsModel::findOrFail($key);
                if ($goodMdoel->ref_payment_id != $penaltyPayment->id || $goodMdoel->ref_book_id != $item) {
                    continue;
                }

                $quantity = $data['quantity'][$key];
                $price = $data['price'][$key];

                $goodMdoel->update([
                    'type' => $data['type'][$key],
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => $quantity * $price,
                ]);
            }
        }
    }
}
