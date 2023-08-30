<?php


namespace LaravelSupports\Libraries\Book\Yes24;


use LaravelSupports\Libraries\Book\Common\Abstracts\AbstractStoreStockService;
use Symfony\Component\DomCrawler\Crawler;

class Yes24StoreStockService extends AbstractStoreStockService
{
    protected $storeCode = 'yes24';
    protected $searchURL = 'http://www.yes24.com/searchcorner/Search?query=';

    public function hasStock()
    {
        $content = $this->call($this->getSearchURL());

        $crawler = new Crawler($content);
        $itemCount = $crawler->filter('.btn_goods_buy_direct')->count();

        // 출고 예상일 정보
        $forwardingDateNode = $crawler->filter('.rdate')->first();
        if (empty($forwardingDateNode)) {
            $forwardingDate = $crawler->filter('.rdate')->first()->text();
            $this->setForwardingDate($forwardingDate);
        }

        // 절판 여부 저장
        if ($itemCount > 0) {
            $isOut = $crawler->filter('.goods_btn')->text();
            if (strpos($isOut, '절판') !== false) {
                $this->setStatus('out');
            }
        }

        // 재고 여부
        $hasStock = false;
        $multiStock = false;
        if ($itemCount == 1) {
            $hasStock = true;
        } else if ($itemCount > 1) {
            $hasStock = true;
            $multiStock = true;
        }

        $this->stock = $hasStock && $multiStock == false ? 1 : 0;

        return $this->stock;
    }

    public function getSearchURL()
    {
        return $this->searchURL.$this->book->isbn;
    }
}
