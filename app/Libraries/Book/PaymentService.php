<?php


namespace LaravelSupports\Libraries\Book;


use Carbon\Carbon;
use FlyBookModels\Books\BookCartModel;
use FlyBookModels\Books\BookPaymentGoodsModel;
use FlyBookModels\Books\BookPaymentModel;
use FlyBookModels\Delivery\ConfigDeliveryCostModel;
use FlyBookModels\Delivery\DeliveryModel;
use FlyBookModels\Members\MemberPointModel;
use FlyBookModels\Payments\PaymentModuleModel;
use Illuminate\Support\Arr;
use LaravelSupports\Libraries\Book\Exceptions\FailedCancelException;
use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseObject;
use LaravelSupports\Libraries\Pay\Common\Exception\CardException;
use LaravelSupports\Libraries\Pay\Common\Exception\PaymentException;
use LaravelSupports\Libraries\Pay\Delivery\DeliveryService;
use LaravelSupports\Libraries\Pay\ImPort\ImPortPay;
use LaravelSupports\Libraries\Pay\Kakao\KakaoPay;
use LaravelSupports\Libraries\Supports\Date\DateHelper;
use LaravelSupports\Libraries\Supports\Objects\HasDataWithDefaultTrait;
use LaravelSupports\Libraries\Supports\String\Traits\ConvertStringTrait;

class PaymentService
{
    use ConvertStringTrait, HasDataWithDefaultTrait;

    protected $member;
    protected $data;
    protected $readyData;
    protected $type;
    protected $price;
    protected $payment;
    protected $goods;
    protected $paymentModule;
    protected $deliveryRuleCode = 'book';
    protected $deliveryRuleModel;
    protected $webHookURL = 'https://api.flybook.kr/v3/book/payment/callback';
    private $services = [
        'kakao_pay' => KakaoPay::class,
        'nice_pay' => ImPortPay::class,
    ];
    private $paidStatus = [
        'paid',
    ];

    private $paidGoodsStatus = [
        'paid',
    ];

    private $cancelStatus = [
        'cancelled'
    ];

    /**
     * PaymentService constructor.
     *
     * @param $member
     * @param array $data
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

    /**
     * @param string $deliveryRuleCode
     */
    public function setDeliveryRuleCode($deliveryRuleCode)
    {
        $this->deliveryRuleCode = $deliveryRuleCode;
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
     * @added   2020-08-25
     * @updated 2020-08-25
     */
    public function getPaymentReadyData()
    {
        $this->goods = $this->getGoodsBookInfo($this->data);

        $this->readyData = [
            'user' => $this->getUserData(),
            'delivery' => $this->getDefaultDeliveryData(),
            'max_quantity' => $this->getMaxQuantity(),
            'goods' => $this->goods
        ];

        return $this->readyData;
    }

    /**
     * 결제에 필요한 정보를 제공합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getPaymentData()
    {
        $this->getPaymentReadyData();

        $this->readyData = array_merge($this->readyData, $this->data);

        return $this->readyData;
    }

    /**
     * 결제 취소에 필요한 정보를 제공합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getCancelableData()
    {
        $paymentModel = $this->payment;
        $goods = $paymentModel->getGoodsListWhereInGoodsStatus($this->paidGoodsStatus);
        $cancelableGoods = $this->getCancelableGoods($goods);
        $totalPrice = $cancelableGoods->map(function ($item) {
            return $item['total_price'];
        })->sum();

        return [
            'delivery' => $this->getDefaultDeliveryData(),
            'goods' => $cancelableGoods,
            'payment' => [
                'total_price' => $totalPrice,
                'delivery_cost' => $totalPrice > 10000 ? $paymentModel->getPaidFirstGood()->delivery->add_cost : $paymentModel->getPaidFirstGood()->delivery->delivery_cost,
                'cancellation_cost' => $totalPrice > 10000 ? $paymentModel->getPaidFirstGood()->delivery->default_cost : 0,
                'pay_amount' => $paymentModel->getTotalPayAmount(),
                'use_point' => $paymentModel->getUsePoint(),
            ],
        ];
    }

    /**
     * 취소 가능한 상품의 정보를 제공합니다.
     *
     * @param $goods
     * @return mixed
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    private function getCancelableGoods($goods)
    {
        return $goods->map(function ($item) {
            $quantity = $item->getQuantityAccordingToStatus();

            if ($quantity) {
                $book = $item->book;
                return [
                    'id' => $item->id,
                    'title' => $book->title,
                    'quantity' => $quantity,
                    'price' => $item->price,
                    'total_price' => $quantity * $item->price,
                    'book_img' => $book->book_img,
                    'author' => $book->author,
                    'publisher' => $book->publisher,
                ];
            }

            return;
        })
            ->filter()
            ->values();
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
        $totalPrice = $this->calcTotalPrice($this->goods);
        $deliveryCost = $this->getDeliveryCost($this->getAddress(), $totalPrice);

        return [
            'name' => $this->getName(),
            'contact' => $this->getContact(),
            'point' => $this->member->getUsablePoint(),
            'post_code' => $this->getPostCode(),
            'address' => $this->getAddress(),
            'detail_address' => $this->getAddressDetail(),
            'delivery_cost' => $deliveryCost,
        ];
    }

    /**
     * 기본 배송비 정보를 제공합니다.
     *
     * @return array
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getDefaultDeliveryData()
    {
        $deliveryRuleModel = $this->getDeliveryRuleModel();
        return [
            'default_cost' => $deliveryRuleModel->delivery_cost,
            'sale_amount' => $deliveryRuleModel->sale_amount,
        ];
    }

    /**
     * 주문 최대 수량 정보를 제공합니다.
     *
     * @return array
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getMaxQuantity()
    {
        $cartService = new CartService();
        return [
            'cart' => $cartService->getMaxCartQuantity(),
            'book' => $cartService->getMaxBookQuantity(),
            'total' => $cartService->getMaxTotalQuantity(),
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
     * 도서 주문에 필요한 도서 정보를 제공 합니다.
     * 장바구니 우선
     *
     * @param
     * @return array
     * @author  seul
     * @added   2020-08-25
     * @updated 2020-08-27
     * 장바구니 추가
     * @updated 2020-10-14
     * 재고 확인
     */
    public function getGoodsBookInfo($data)
    {
        $bookCarts = BookCartModel::getCartPartListWithBook($this->member->id, $data['carts']);

        return $bookCarts->filter(function($item) {
            $book = $item->book;
            $stockService = new StockService($book);
            $storeStock = $stockService->findStock();
            return $storeStock != null && $storeStock->stock > 0;
        })->map(function ($item) {
            $book = $item->book;
            $storeStock = $book->getStock();
            return [
                'cart_id' => $item->id,
                'book_id' => $item->ref_book_id,
                'quantity' => $item->quantity,
                'title' => $book->title,
                'book_img' => $book->book_img,
                'publisher' => $book->publisher,
                'author' => $book->author,
                'sales_price' => $book->sales_price,
                'stock_status' => $book->stock_status,
                'jego' => is_null($storeStock) ? 0 : $storeStock->stock,
                'store_id' => is_null($storeStock) ? 0 : $storeStock->ref_ordered_store_id,
                'forwarding_date' => is_null($storeStock) ? null : $storeStock->forwarding_date,
            ];
        });
    }

    /**
     * 결제 준비를 합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-08-25
     * @updated 2020-08-25
     */
    public function ready()
    {
        $options = [
            'description' => $this->getDescription(),
            'use_point' => $this->data['use_point'],
        ];

        $paymentModel = BookPaymentModel::createModel($this->paymentModule, $this->member, $options);
        $this->createGoodsModel($paymentModel);
        $totalPrice = $paymentModel->getTotalPriceWhereInGoodsStatus(['ready']);
        $delivertCostArray = $this->getDeliveryCost($this->getAddress(), $totalPrice);

        $data = [
            'name' => $this->data['user_name'],
            'phone' => $this->data['user_contact'],
            'post_code' => $this->data['delivery_post_code'],
            'address' => $this->data['delivery_address'],
            'address_detail' => $this->data['delivery_detail_address'],
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
        }
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
    private function throwCardInformation()
    {
        if ( $this->readyData != null && $this->readyData['type'] == 'nice_pay' && (isset($this->readyData['card']) == false || $this->readyData['card'] == null) ) {
            $cardNumber = isset($this->readyData['card_number']) ? $this->readyData['card_number'] : null;
            $cardPassword = isset($this->readyData['card_password']) ? $this->readyData['card_password'] : null;
            $cardBirth = isset($this->readyData['card_month']) ? $this->readyData['card_month'] : null;
            $cardExpiry = isset($this->readyData['card_year']) ? $this->readyData['card_year'] : null;

            throw_if(is_null($cardNumber) || is_null($cardPassword) || is_null($cardBirth) || is_null($cardExpiry), new CardException());
        }
    }

    /**
     * 주문 생성 시 description을 제공합니다.
     *
     * @return mixed|string
     * @author  seul
     * @added   2020-08-26
     * @updated 2020-08-26
     */
    private function getDescription()
    {
        $title = $this->shortenString($this->goods->first()['title']);
        $count = count($this->goods) - 1;
        if ($count > 0) {
            $title .= " 외 {$count}건";
        }

        return $title;
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
    public function getDeliveryCost($delivery_address, $totalPrice)
    {
        $defaultCost = $this->deliveryRuleModel->getDefaultDeliveryCost($totalPrice);

        $deliveryService = new DeliveryService();
        $addCost = $deliveryService->getDeliveryCost($delivery_address);

        return [
            'default_cost' => $defaultCost,
            'add_cost' => $addCost,
            'delivery_cost' => $defaultCost + $addCost,
        ];
    }

    public function createGoodsModel($paymentModel)
    {
        $goods = $this->getGoods();

        /**
         * storeID가 북센이 아닌 상품이 있으면 공백처리
         *(준현님 요청사항)
         *
         * @author  seul
         * @added   2020-10-15
         * @updated 2020-10-15
         */
        $storeIDs = array_unique(Arr::pluck($goods, 'store_id'));
        if (in_array(1, $storeIDs) == false || count($storeIDs) > 1) {
            $storeID = null;
        } else {
            $storeID = 1; // 북센
        }
        $data = [
            'ref_book_payment_id' => $paymentModel->id,
            'ref_book_ordered_store_id' => $storeID,
            'status' => 'ready',
        ];

        foreach ($goods as $good) {
            $data['ref_book_id'] = $good['book_id'];
            $data['quantity'] = $good['quantity'];
            $data['price'] = $good['sales_price'];
            $data['total_price'] = $good['quantity'] * $good['sales_price'];
            $data['forwarding_date'] = $good['forwarding_date'];

            BookPaymentGoodsModel::createModel($data);
        }
    }

    public static function createServiceWithPayment(BookPaymentModel $payment)
    {
        $service = new self(
            $payment->getMemberModel(),
            ['type' => $payment->getType()]
        );
        $service->payment = $payment;
        return $service;
    }

    /**
     * 주문하려는 도서의 판매 금액을 합산하여 제공합니다
     *
     * @param $goodsInfo
     * @return mixed
     * @author  seul
     * @added   2020-08-26
     * @updated 2020-08-26
     */
    public function calcTotalPrice($goodsInfo)
    {
        $totalPrice = collect($goodsInfo)->map(function ($item, $key) {
            return $item['quantity'] * $item['sales_price'];
        })->sum();

        return $totalPrice;
    }

    /**
     * 해당 주문건의 구매 금액을 제공합니다.
     *
     * @return
     * @author  seul
     * @added   2020-08-26
     * @updated 2020-08-26
     */
    public function getTotalPrice()
    {
        return $this->payment->getTotalPrice();
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return mixed
     */
    public function getGoods()
    {
        return $this->goods;
    }

    private function bindResponseReady(BookPaymentModel $payment, AbstractResponseObject $result)
    {
        $payment->setTID($result->getTID());
        $payment->save();
        return $payment;
    }

    private function bindResponseApprove(BookPaymentModel $payment, AbstractResponseObject $result)
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
     * 실 결제 금액이 0보다 클 경우 결제를 진행합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-09-15
     * @updated 2020-09-15
     */
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

    /**
     * 취소가 가능한 주문건의 경우 취소를 진행합니다.
     *
     * @param $cancelIDArray
     * @param $cancelQuantityArray
     * @return mixed
     * @throws \Throwable
     * @author  seul
     * @added   2020-08-31
     * @updated 2020-08-31
     */
    public function cancel($cancelIDArray, $cancelQuantityArray)
    {
        $cancelList = collect($cancelIDArray)->combine($cancelQuantityArray);

        $payAmount = $this->payment->getTotalPayAmount();
        $cancelRequestAmount = $this->getCancelAmount($cancelIDArray, $cancelList);

        throw_if($cancelRequestAmount == 0, new FailedCancelException());

        $this->bindCancelGoods($this->payment, $cancelList);
        $cancelAmount = $this->calcCancelAmount($payAmount, $cancelRequestAmount);

        $data = [
            'cancel_amount' => $this->payment->isAllCancelledGoods() ? $this->payment->getTotalPayAmount() : $cancelAmount,
            'reason' => '고객 요청 취소',
            'cancel_list' => $cancelList
        ];

        if ($data['cancel_amount']) {
            $services = new $this->services[$this->payment->ref_payment_module_code]($this->member, $this->payment, null, $data);

            $services->cancel();
        }

        $status = $this->payment->getCancelStatus();
        $this->payment->status = $status;
        $this->payment->cancel_amount += $data['cancel_amount'];
        $this->payment->cancelled_at = $this->payment->cancelled_at ? $this->payment->cancelled_at : now();
        $this->payment->save();
    }

    /**
     * 결제 전체취소를 진행합니다.
     *
     * @return void
     * @author  seul
     * @added   2020-09-21
     * @updated 2020-09-21
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

        $payment->status = 'cancelled';
        $payment->cancel_amount += $cancelAmount;
        $payment->cancel_point += $cancelPoint;
        $payment->cancelled_at = now();
        $payment->save();

        $paidGoods = $payment->wherePaidGoods()->get();
        $paidGoods->each(function ($item) use ($payment) {
            $bookID = $item->ref_book_id;

            $cancelledGood = BookPaymentGoodsModel::where('ref_book_payment_id', $payment->id)
                ->where('status', 'cancelled')
                ->where('ref_book_id', $bookID)->first();

            if ( is_null($cancelledGood) ) {
                $cancelledGood = new BookPaymentGoodsModel();

                $cancelledGood->ref_book_payment_id = $payment->id;
                $cancelledGood->status = 'cancelled';
                $cancelledGood->ref_book_id = $bookID;
            }

            $cancelledGood->quantity = $item->quantity;
            $cancelledGood->cancelled_at = now();
            $cancelledGood->save();
        });
    }

    /**
     * 취소 금액을 계산하여 제공하고, 배송비를 업데이트 합니다.
     *
     * @param $payAmount
     * @param $requestAmount
     * @return
     * @author  seul
     * @added   2020-09-15
     * @updated 2020-09-15
     */
    private function calcCancelAmount($payAmount, $requestAmount)
    {
        if ($payAmount > 0) {
            $cancelAmount = $requestAmount > $payAmount ? $payAmount : $requestAmount;
        } else {
            $cancelAmount = $requestAmount;
        }

        $isAllCancelledPayment = $this->payment->isAllCancelledGoods();
        $good = $this->payment->getPaidFirstGood();
        $delivery = $good != null ? $good->delivery : null;

        // 배송비 업데이트
        if ($isAllCancelledPayment) {
            $deilveries = $this->payment->deliveries()->get();
            foreach ($deilveries as $item) {
                $item->update([
                    'default_cost' => 0,
                    'add_cost' => 0,
                    'delivery_cost' => 0,
                ]);
            }
        } else {
            $totalPayAmount = $this->payment->getTotalPayAmount() + $this->payment->getUsePoint();
            $balancePayAmount = $totalPayAmount - $cancelAmount;
            if ($balancePayAmount > 0) {
                $defaultCost = $this->deliveryRuleModel->getDefaultDeliveryCost($balancePayAmount);

                if ($balancePayAmount < $this->deliveryRuleModel->sale_amount) {
                    $good = $this->payment->getPaidFirstGood();
                    $delivery = $good->delivery;

                    if ($delivery != null && $delivery->default_cost == 0) {
                        $delivery->default_cost = $defaultCost;
                        $delivery->delivery_cost = $delivery->default_cost + $delivery->add_cost;
                        $delivery->save();

                        $cancelAmount -= $defaultCost;
                    }
                }
            }
        }

        $deliveryCost = $delivery != null ? $delivery->delivery_cost : 0;

        // 포인트 환급
        if ($isAllCancelledPayment) {
            $point = $this->payment->getUsePoint();
        } else {
            if ($payAmount > 0) {
                $point = $totalPayAmount + $deliveryCost == $requestAmount ? $this->payment->getUsePoint() : $requestAmount - $cancelAmount - $deliveryCost;
            } else {
                $point = $totalPayAmount == $requestAmount ? $this->payment->getUsePoint() : $cancelAmount;
            }
        }

        if ($point > 0) {
            $memo = $this->payment->description . ' 취소건으로 인한 포인트 환급';
            $destroyedAt = Carbon::now()->addMonth(6)->format('Y-m-d');
            MemberPointModel::createPoint($point, $this->member->id, $memo, $destroyedAt);

            $this->payment->cancel_point += $point;
            $this->payment->save();
        }

        return $payAmount > 0 ? $cancelAmount : 0;
    }

    /**
     * 취소 금액을 제공합니다.
     *
     * @param $cancelIDArray
     * @param $cancelList
     * @return mixed
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    public function getCancelAmount($cancelIDArray, $cancelList)
    {
        $cancelAmount = $cancelList->map(function ($item, $key) {
            $good = BookPaymentGoodsModel::find($key);
            $quantity = $good->getQuantityAccordingToStatus();
            $quantity = $quantity > $item ? $item : $quantity;

            return $good->price * $quantity;
        })
            ->sum();

        return $cancelAmount;
    }

    /**
     * 취소 상품 정보를 업데이트 합니다
     *
     * @param BookPayment $bookPayment
     * @param $data
     * @return void
     * @author  seul
     * @added   2020-09-03
     * @updated 2020-09-03
     */
    private function bindCancelGoods(BookPayment $bookPayment, $cancelList)
    {
        $paymentID = $bookPayment->id;
        $cancelList->each(function ($item, $key) use ($paymentID) {
            $good = BookPaymentGoodsModel::find($key);
            $cancelGood = BookPaymentGoodsModel::findCancelGoodWhereBookID($paymentID, $good->ref_book_id);

            if ($cancelGood) {
                $cancelGood->quantity += $item;
                $cancelGood->total_price = $cancelGood->quantity * $good->price;
                $cancelGood->save();
            } else {
                BookPaymentGoodsModel::createModel([
                    'ref_book_payment_id' => $good->ref_book_payment_id,
                    'ref_book_id' => $good->ref_book_id,
                    'status' => 'cancelled',
                    'quantity' => $item,
                    'price' => $good->price,
                    'total_price' => $item * $good->price,
                    'canceled_at' => now(),
                ]);
            }
        });
    }
}
