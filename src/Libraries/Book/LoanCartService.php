<?php


namespace LaravelSupports\Libraries\Book;


use LaravelSupports\Libraries\Book\Abstracts\AbstractCartService;
use LaravelSupports\Libraries\Book\Exceptions\AlreadyExistsException;
use LaravelSupports\Libraries\Book\Exceptions\DestroyException;
use LaravelSupports\Libraries\Book\Exceptions\OverQuantityException;

class LoanCartService extends AbstractCartService
{
    protected $maxCartQuantity  = 30;
    protected $maxBookQuantity  = 1;
    protected $maxTotalQuantity = 30;

    /**
     * 회원의 대여할 책 수량을 제공합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2021-01-27
     * @updated 2021-01-27
     */
    public function getCartCount()
    {
        return LoanBookCartModel::countCart($this->member->id);
    }

    public function getCartList()
    {
        $loanScheduleService = new LoanScheduleService();
        $LoanScheduleTime = $loanScheduleService->calcScheduledDeliveryTime();
        $carts = $this->bindCarts();
        return [
            'free_loanable_count' => (int)$this->member->getMemberBenefit('delivery_loanable_count'),
            'loan_cost' => 3000,
            'count' => count($carts),
            'loan_scheduled_delivery_date' => $loanScheduleService->convertScheduledDeliveryDate($LoanScheduleTime, 'm월 d일'),
            'delivery_loanable_days' => (int)$this->member->getMemberBenefit('delivery_loanable_days'),
            'carts' => [
                [
                    'title' => $loanScheduleService->convertScheduledDeliveryDate($LoanScheduleTime).' 도착 예정',
                    'books' => $carts
                ],
            ],
        ];
    }

    /**
     * 대여 가능한 책 분류를 합니다.
     *
     * @param
     * @return
     * @author  seul
     * @added   2021-01-27
     * @updated 2021-01-27
     *  장바구니 리스트를 array로 한 번 더 묶어서 제공 (앱 개발 요청사항)
     * @updated 2021-01-28
     */
    protected function bindCarts()
    {
        $carts = LoanBookCartModel::getCartListWithBook($this->member->id);
        return $carts->transform(function ($item) {
            $loanStockService = new LoanStockService($item->book);
            $item->status = $loanStockService->getLoanStatus();

            return $item;
        });
    }


    public function storeCart($quantity, $book)
    {
        $data = [
            'ref_member_id' => $this->member->id,
            'ref_book_id' => $book->id,
        ];

        throw_if(LoanBookCartModel::where($data)->exists(), new AlreadyExistsException());

        array_push($data, [
            'quantity' => $quantity
        ]);

        $bookCart = LoanBookCartModel::create($data);

        $this->throwCartException();

        return $bookCart->id;
    }

    public function throwCartException()
    {
        $maxCartQuantity  = $this->getMaxCartQuantity();
        $maxTotalQuantity = $this->getMaxTotalQuantity();

        $carts = $this->bindCarts();

        $totalQuantity = $carts->sum('quantity');

        // 장바구니에 담을 수 있는 최대 책 종류 오류
        throw_if(count($carts) > $maxCartQuantity, new OverQuantityException("장바구니에 담을 수 있는 책의 종류는 최대 {$maxCartQuantity}권 입니다."));

        // 책의 전체 수량이 설정된 최대 수량보다 많을 경우 오류
        throw_if($totalQuantity > $maxTotalQuantity, new OverQuantityException("장바구니에 담을 수 있는 책의 전체 수량은 최대 {$maxTotalQuantity}권 입니다."));
    }

    public function deleteCart($deleteIDArray)
    {
        $carts = LoanBookCartModel::whereInCartIDsAndMemberID($this->member->id, $deleteIDArray);
        $cartsCount = $carts->count();

        throw_if(count($deleteIDArray) != $cartsCount, new DestroyException());

        return $carts->delete();
    }
}
