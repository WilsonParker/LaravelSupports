<?php


namespace LaravelSupports\Libraries\Book;



use FlyBookModels\Books\BookCartModel;
use LaravelSupports\Libraries\Book\Exceptions\DestroyException;
use LaravelSupports\Libraries\Book\Exceptions\OverQuantityException;

class CartService
{
    protected $member;
    protected $isOverlap        = true;
    protected $maxCartQuantity  = 10;
    protected $maxBookQuantity  = 99;
    protected $maxTotalQuantity = 990;

    /**
     * CartService constructor.
     * @param $member
     */
    public function __construct($member = null)
    {
        $this->member = $member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * 카트에 담을 수 있는 최대 상품 종류 갯수
     *
     * @return int
     */
    public function getMaxCartQuantity()
    {
        return $this->maxCartQuantity;
    }

    /**
     * 카트에 담을 수 있는 책 하나당 최대 갯수
     *
     * @return int
     */
    public function getMaxBookQuantity()
    {
        return $this->maxBookQuantity;
    }

    /**
     * 카트에 담을 수 있는 장바구니 총 수량 최대 갯수
     *
     * @return int
     */
    public function getMaxTotalQuantity()
    {
        return $this->maxTotalQuantity;
    }

    /**
     * 장바구니와 책의 정보를 제공 합니다.
     *
     * @return
     * @author  seul
     * @added   2020-08-20
     * @updated 2020-08-20
     */
    public function getCartList()
    {
        $paymentService = new PaymentService();
        $deliveryRuleModel = $paymentService->getDeliveryRuleModel();
        $carts = $this->bindCarts();

        return [
            'delivery' => [
                'default_cost' => $deliveryRuleModel->delivery_cost,
                'sale_amount'  => $deliveryRuleModel->sale_amount,
            ],
            'count' => count($carts),
            'carts' => $carts,
        ];
    }

    /**
     * 도서 재고 확인 후 장바구니 정보를 제공합니다.
     *
     * @return
     * @author  seul
     * @added   2020-10-06
     * @updated 2020-10-06
     * 실시간 재고 확인
     */
    private function bindCarts()
    {
        $carts = $this->member->getCarts();
        return $carts->transform(function ($item) {
            $book = $item->book;
            $bookStock = $this->bindStock($book);

            $item->status = $bookStock->stock > 0 ? 'normal' : 'sold_out';

            return $item;
        });
    }

    /**
     * 실시간 재고 정보를 확인합니다.
     *
     * @param
     * @return void
     * @author  seul
     * @added   2020-10-08
     * @updated 2020-10-08
     */
    private function bindStock($book)
    {
        $service = new StockService($book);
        return $service->getBookStock();
    }

    /**
     * 장바구니에 책을 등록합니다.
     * is_overlap : true일 경우 기존 장바구니에 수량 추가, false일 경우 기존 장바구니 유지 및 신규 책만 등록
     *
     * @param $data
     * @return void
     * @author  seul
     * @added   2020-08-20
     * @updated 2020-08-20
     * @updated 2020-10-08
     * 품절 도서일 경우 장바구니 추가 실패
     */
    public function storeCart($quantity, $book)
    {
        $memberID = $this->member->id;
        $maxBookQuantity = $this->getMaxBookQuantity();

        $bookCart = BookCartModel::getCartOrNew($memberID, $book->id);

        if ( $this->isOverlap ) {
            $bookCart->quantity += $quantity;
        } else if (isset($bookCart->ref_book_id) == false) {
            $bookCart->ref_book_id   = $book->id;
            $bookCart->ref_member_id = $memberID;
            $bookCart->quantity      = $quantity;
        }

        // 설정된 책 1종당 최대 구매 가능 수량보다 요청 수량이 많을 경우 최다 구매 수량으로 설정
        $bookCart->quantity = $bookCart->quantity > $maxBookQuantity ? $maxBookQuantity : $bookCart->quantity;

        $bookCart->save();

        $this->throwCartException();

        return $bookCart->id;
    }

    /**
     * 장바구니에 있는 책의 수량을 변경합니다.
     *
     * @param $data
     * @param BookCartModel $cart
     * @return void
     * @author  seul
     * @added   2020-08-21
     * @updated 2020-08-21
     */
    public function updateCart($data, BookCartModel $cart)
    {
        $cart->quantity = $data['quantity'] > $this->getMaxBookQuantity() ? $this->getMaxBookQuantity() : $data['quantity'];

        return $cart->save();
    }

    /**
     * 선택한 책을 장바구니에서 제외합니다.
     *
     * @param $deleteIDArray
     * @return void
     * @throws \Throwable
     * @author  seul
     * @added   2020-08-21
     * @updated 2020-08-21
     */
    public function deleteCart($deleteIDArray)
    {
        $carts = BookCartModel::whereInCartIDsAndMemberID($this->member->id, $deleteIDArray);
        $cartsCount = $carts->count();

        throw_if(count($deleteIDArray) != $cartsCount, new DestroyException());

        return $carts->delete();
    }

    /**
     * 장바구니 수량에 따른 오류를 발생시킵니다.
     *
     * @return void
     * @throws \Throwable
     * @author  seul
     * @added   2020-08-31
     * @updated 2020-08-31
     * @updated 2020-10-08
     * 컨트롤러에서 CartService로 옮김
     */
    private function throwCartException()
    {
        $maxCartQuantity  = $this->getMaxCartQuantity();
        $maxTotalQuantity = $this->getMaxTotalQuantity();

        $carts = $this->member->getCarts();

        $totalQuantity = $carts->sum('quantity');

        // 장바구니에 담을 수 있는 최대 책 종류 오류
        throw_if(count($carts) > $maxCartQuantity, new OverQuantityException("장바구니에 담을 수 있는 책의 종류는 최대 {$maxCartQuantity}권 입니다."));

        // 책의 전체 수량이 설정된 최대 수량보다 많을 경우 오류
        throw_if($totalQuantity > $maxTotalQuantity, new OverQuantityException("장바구니에 담을 수 있는 책의 전체 수량은 최대 {$maxTotalQuantity}권 입니다."));
    }
}
