<?php


namespace LaravelSupports\Libraries\Book\Abstracts;


abstract class AbstractCartService
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

    abstract public function getCartList();

    abstract public function storeCart($quantity, $book);

    abstract public function deleteCart($deleteIDArray);

    abstract public function throwCartException();
}
