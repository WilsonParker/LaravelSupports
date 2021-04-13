<?php

namespace LaravelSupports\Views\Components\Books;


use FlyBookModels\Books\LoanBookPaymentGoodsModel;
use Illuminate\Database\Eloquent\Collection;
use LaravelSupports\Views\Components\BaseComponent;

class BookLoanGoodsComponent extends BaseComponent
{
    protected string $view = 'book.book_loan_goods_component';

    public Collection $goods;

    public $goodsStatusArray = [
        'paid' => '상품준비중',
        'preparing' => '상품준비중',
        'ordered' => '주문완료',
        'shipping' => '발송완료',
        'cancelled' => '주문취소',
        'failed' => '주문실패',
        'exchanged' => '교환',
        'return' => '반품',
    ];

    /**
     * Create a new component instance.
     *
     * @param Collection $goods
     */
    public function __construct(Collection $goods)
    {
        $this->goods = $goods;
    }
}
