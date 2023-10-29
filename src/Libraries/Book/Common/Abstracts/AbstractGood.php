<?php

namespace LaravelSupports\Libraries\Book\Common\Abstracts;


use FlyBookModels\Books\BookCartModel;

abstract class AbstractGood
{
    /**
     * 장바구니에서 상품 정보를 제공합니다.
     *
     * @param $carts
     * @return
     * @author  seul
     * @added   2020-09-22
     * @updated 2020-09-22
     */
    public function bindGoodsFromCart($carts)
    {
        $bookCarts = BookCartModel::getCartPartListWithBook($this->member->id, $carts);

        return $bookCarts->map(function ($item) {
            $book = $item->book;
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
                'jego' => $book->jego,
            ];
        })
            ->values();
    }

    /**
     * 책 ID와 수량으로 장바구니에 담고 장바구니 ID를 제공합니다.
     *
     * @param $bookID
     * @param $quantity
     * @return mixed
     * @author  seul
     * @added   2020-09-22
     * @updated 2020-09-22
     */
    public function bindCartIDFromBookIDAndQuantity($bookID, $quantity)
    {
        $data = [
            'ref_member_id' => $this->member->id,
            'ref_book_id' => $bookID,
            'quantity' => $quantity,
            'is_direct' => 'Y',
        ];
        $bookCart = BookCartModel::createModel($data);

        return $bookCart->id;
    }
}
