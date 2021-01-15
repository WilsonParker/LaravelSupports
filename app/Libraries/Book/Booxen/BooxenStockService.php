<?php


namespace LaravelSupports\Libraries\Book\Booxen;

use App\Services\Book\Common\Abstracts\AbstractStockService;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class BooxenStockService extends AbstractStockService
{
    protected $storeCode = '북센';
    protected $apiURL = 'https://api-b.booxen.com/API/BookSaleInfo';
    protected $companyID = 50613;
    protected $apiKey = 'xmyox993wmmdg8b806mstbolwg0bvt';

    public function hasStock()
    {
        $client = new Client();
        $response = $client->request('GET', $this->apiURL, [
            'query' => [
                'CompCD' => $this->companyID,
                'APIKey' => $this->apiKey,
                'EanCD' => $this->book->isbn,
            ],
        ]);

        $contents = $response->getBody()->getContents();
        preg_match("/callback\((.*)\)/", $contents, $matches);
        $returnData = @json_decode($matches[1], true);

        $stock = 0;
        if ($returnData['RESULTCD'] == '200') {
            $stock = $returnData['QTY'];
            $status = $returnData['STATUS'];

            switch ($status) {
                case '구판':
                   $this->setStatus('old');
                    break;
                case '구판절판':
                case '절판':
                   $this->setStatus('out');
                    break;
                case '정상':
                case '품절':
                case '장기품절':
                case '비거래':
                    break;
                default:
                    $data = [
                        'book' => $this->book,
                        'data' => $status,
                    ];
                    break;
            }
        }

        $this->setStock($stock);

        return $stock;
    }

    public function getSearchURL()
    {
        return $this->searchURL.$this->book->isbn;
    }

    /**
     * 로그인 정보를 제공합니다.
     *
     * @return CookieJar
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author  seul
     * @added   2020-10-07
     * @updated 2020-10-07
     * @author  seul
     * @added   2020-12-17
     * API 사용으로 사용 안함
     */
    private function getLoginData()
    {
        $response = $this->buildRequest($this->getLoginURL(), 'POST', [
            'form_params' => [
                'userId' => 'flybook01',
                'passwd' => 'flybook5!',
            ],
        ]);

        $cookies = explode(';', $response->getHeaders()['Set-Cookie'][0]);

        return CookieJar::fromArray([
            'JSESSIONID' => str_replace('JSESSIONID=', '', $cookies[0]),
        ], 'orderbook.booxen.com');
    }

    /**
     * 북센에서 절판된 도서가 yes24에서 주문이 가능한 경우가 있어
     * return false 추가함
     *
     * @param
     * @return
     * @author  seul
     * @added   2020-10-15
     * @updated 2020-10-15
     */
    public function isOut()
    {
        return false;
    }
}
