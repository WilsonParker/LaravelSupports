<?php

namespace LaravelSupports\Libraries\Book\Common\Abstracts;

use FlyBookModels\Books\BookModel;
use FlyBookModels\Books\BookOrderedStoreModel;
use GuzzleHttp\Client;

abstract class AbstractStoreStockService
{
    protected $storeCode;
    protected $book;
    protected $stock;
    protected $store;
    protected $forwardingDate;
    protected $status = 'sale';
    protected $loginURL;
    protected $searchURL;

    /**
     * AbstractStockService constructor.
     * @param $book
     */
    public function __construct(BookModel $book)
    {
        $this->book = $book;
        $this->store = BookOrderedStoreModel::findModelWhereCode($this->storeCode);

        $this->init();
    }

    protected function init()
    {

    }

    /**
     * 크롤링한 컨텐츠를 제공합니다.
     *
     * @param $url
     * @param $method
     * @param $options
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author  seul
     * @added   2020-10-05
     * @updated 2020-10-05
     */
    protected function call ($url, $method = 'get', $options = [])
    {
        $response = $this->buildRequest($url, $method, $options);
        return $response->getBody()->getContents();
    }

    /**
     * response를 생성합니다.
     *
     * @param $url
     * @param $method
     * @param $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author  seul
     * @added   2020-10-05
     * @updated 2020-10-05
     */
    protected function buildRequest($url, $method, $options)
    {
        $client = new Client();
        $response = $client->request($method, $url, $options);
        return $response;
    }

    /**
     * 재고 확인을 진행합니다.
     *
     * @return void
     * @author  seul
     * @added   2020-10-06
     * @updated 2020-10-06
     */
    public function hasStock()
    {

    }

    /**
     * 재고 정보를 제공합니다.
     *
     * @return array
     * @author  seul
     * @added   2020-10-06
     * @updated 2020-10-06
     */
    public function getStockInformation()
    {
        return [
            'store_id' => $this->getStoreID(),
            'book_id' => $this->getBookID(),
            'stock' => $this->getStock(),
            'forwarding_date' => $this->getForwardingDate(),
            'status' => $this->getStatus(),
        ];
    }

    public function getStoreID()
    {
        return $this->store->id;
    }

    public function getBookID()
    {
        return $this->book->id;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getLoginURL()
    {
        return $this->loginURL;
    }

    /**
     * @return mixed
     */
    public function getSearchURL()
    {
        return $this->searchURL;
    }

    /**
     * @return mixed
     */
    public function getForwardingDate()
    {
        return $this->forwardingDate;
    }

    /**
     * @param mixed $forwardingDate
     */
    public function setForwardingDate($forwardingDate)
    {
        $this->forwardingDate = $forwardingDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * 품절 상태를 확인합니다.
     *
     * @return bool
     * @author  seul
     * @added   2020-10-12
     * @updated 2020-10-12
     */
    public function isOut()
    {
        $bookModel = new BookModel();
        $outStatus = $bookModel->outStatus;

        return in_array($this->getStatus(), $outStatus);
    }
}
