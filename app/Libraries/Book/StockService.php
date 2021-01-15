<?php


namespace LaravelSupports\Libraries\Book;


use FlyBookModels\Books\BookModel;
use FlyBookModels\Books\BookStoreStockModel;
use LaravelSupports\Libraries\Book\Booxen\BooxenStockService;
use LaravelSupports\Libraries\Book\Yes24\Yes24StockService;

class StockService
{
    protected $book;
    private $services = [
        'booxen' => BooxenStockService::class,
        'yes24' => Yes24StockService::class,
    ];

    /**
     * StockService constructor.
     * @param Book $book
     */
    public function __construct(BookModel $book)
    {
        $this->book = $book;
    }

    /**
     * 재고를 확인하여 정보를 제공합니다.
     *
     * @return void
     * @author  seul
     * @added   2020-10-06
     * @updated 2020-10-06
     */
    public function getBookStock()
    {
        $stock = null;
        if ($this->hasStock()) {
            $stock = $this->findStock();
        } else {
            $lastKey = array_key_last($this->services);
            foreach ($this->services as $code => $serviceClass) {
                $service = new $serviceClass($this->book);
                // 마지막 코드일 경우 재고가 없어도 저장
                if ($service->hasStock() || $service->isOut() || $code == $lastKey) {
                    $stock = $this->storeStock($service->getStockInformation());
                    break;
                }
            }
        }

        return $stock;
    }

    public function hasStock()
    {
        return $this->findStock() != null;
    }

    public function findStock()
    {
        return BookStoreStockModel::findStock($this->book->id);
    }

    private function storeStock($data)
    {
        BookModel::find($this->book->id)->update(['status' => $data['status']]);

        return BookStoreStockModel::create([
            'ref_ordered_store_id' => $data['store_id'],
            'ref_book_id' => $data['book_id'],
            'stock' => $data['stock'],
            'forwarding_date' => $data['forwarding_date'],
        ]);
    }
}
