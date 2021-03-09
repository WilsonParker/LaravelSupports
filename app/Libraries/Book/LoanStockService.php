<?php

namespace LaravelSupports\Libraries\Book;


use FlyBookModels\Books\LoanBookPaymentGoodsModel;
use FlyBookModels\Offline\OfflineLoanBookModel;

class LoanStockService extends AbstractStockService
{
    public function getBookStock()
    {
        $hasStock = $this->hasStock();

        $result = [
            'loan_store' => null,
            'loanable' => false,
            'has_stock' => $hasStock,
            'loanable_quantity' => 0,
        ];

        if ($hasStock) {
            $bookStock = $this->findStock();
            $result['loanable_quantity'] = $bookStock->remained_quantity;
            if ($result['loanable_quantity'] > 0) {
                $result['loan_store'] = $bookStock->offlineStore->code;
                $result['loanable'] = true;
            }
        } else {
            $service = new StockService($this->book);
            $bookStock = $service->getBookStock();

            $stock = $bookStock->stock;
            $store = $bookStock->orderedStore;
            $storeCode = is_null($store) ? null : $store->code;

            if ($storeCode == 'booxen' && $stock) {
                /**
                 * 금일 주문건이 있을 경우 제외
                 *
                 * @author  seul
                 * @added   2021-02-05
                 * @updated 2021-02-05
                 */
                $date = date('Y-m-d');
                $hasOrder = LoanBookPaymentGoodsModel::existsOrderedBook($this->book->id, 1, [$date.' 00:00:00', $date.' 23:59:59']);

                $result['loanable_quantity'] = !$hasOrder ? $stock : 0;
                $result['loan_store'] = $storeCode;
                $result['loanable'] = true;
            }
        }

        return $result;
    }

    public function findStock()
    {
        return OfflineLoanBookModel::whereHasStock($this->book->id)
            ->with('offlineStore')
            ->first();
    }

    public function hasStock()
    {
        return OfflineLoanBookModel::whereHasStock($this->book->id)
            ->exists();
    }

    public function getLoanStatus()
    {
        $status = 'loanable';
        if (!$this->hasStock()) {
            $status = 'loaned';
            $stockService = new StockService($this->book);
            $bookStock = $stockService->getBookStock();

            $store = isset($bookStock->orderedStore) ? $bookStock->orderedStore->code : '';
            if ($store == 'booxen' && $bookStock->stock > 0) {
                $date = date('Y-m-d');
                $hasOrder = LoanBookPaymentGoodsModel::existsOrderedBook($this->book->id, 1, [$date.' 00:00:00', $date.' 23:59:59']);
                $status = $hasOrder ? $status : 'loanable';
            }
        }

        return $status;
    }
}
