<?php


namespace LaravelSupports\Libraries\BookLibrary\Api;


use FlyBookModels\Books\BookModel;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use LaravelSupports\Libraries\BookLibrary\Api\Response\NaverSearchBookResponse;
use SoapBox\Formatter\Formatter;

class NaverSearchBookAPI
{
    private string $url = 'https://openapi.naver.com/v1/search/book_adv.json';

    public function searchForISBN($isbn): NaverSearchBookResponse
    {
        $client = new Client();
        $response = $client->request('GET', $this->url, [
            'headers' => [
                'X-Naver-Client-Id' => 'Y8CezI0SlnABjwknC97o',
                'X-Naver-Client-Secret' => 'tOPjuhnGA3',
            ],
            'query' => [
                'd_isbn' => $isbn,
            ]
        ]);
        $result = json_decode($response->getBody()->getContents());
        $model = new NaverSearchBookResponse();
        $model->bindStd($result);
        return $model;
    }

    public function searchForKeyword($keyword):Collection
    {
        $client = new Client();
        $this->url = 'https://openapi.naver.com/v1/search/book.xml';

        $keyword = collect(explode(' ', $keyword))->transform(function ($item) {
            return urlencode($item);
        })->toArray();
        $keyword = implode('+', $keyword);

        $response = $client->request('GET', $this->url.'?query='.$keyword, [
            'headers' => [
                'X-Naver-Client-Id' => 'Y8CezI0SlnABjwknC97o',
                'X-Naver-Client-Secret' => 'tOPjuhnGA3',
            ]
        ]);

        $data = $response->getBody()->getContents();
        $formatter = Formatter::make($data, Formatter::XML);

        return $this->bindReturnData($formatter->toArray());
    }

    protected function bindReturnData($returnData)
    {
        if(!$returnData['channel']) {
            return null;
        }

        $returnData['channel']['total'] = (int) $returnData['channel']['total'];
        $returnData['channel']['display'] = (int) $returnData['channel']['display'];
        $returnData['channel']['start'] = (int) $returnData['channel']['start'];

        if($returnData['channel']['total'] > 0) {
            $returnData['channel']['last_page'] = ceil($returnData['channel']['total'] / $returnData['channel']['display']);
        }

        if($returnData['channel']['total'] == 1) {
            $item = $returnData['channel']['item'];

            if(gettype($item['isbn']) == 'array') $item['isbn'] = '';
            if(gettype($item['description']) == 'array') $item['description'] = '';
            if(gettype($item['image']) == 'array') $item['image'] = '';
            if(gettype($item['author']) == 'array') $item['author'] = '';
            if(gettype($item['publisher']) == 'array') $item['publisher'] = '';
            if(gettype($item['pubdate']) == 'array') $item['pubdate'] = '';

            if ($item['isbn'] != '') {
                $item['price'] = gettype($item['price']) == 'array' ? 0 : (int)$item['price'];
                $item['discount'] = gettype($item['discount']) == 'array' ? $item['price'] : (int)$item['discount'];

                if(strpos($item['author'], '|') !== false) {
                    $tmparr = explode('|', $item['author']);
                    $item['author'] = $tmparr[0].' 외 '.(count($tmparr)-1).'명';
                }

                if(strpos($item['isbn'], " ") !== false) {
                    $item['isbn'] = substr($item['isbn'], strpos($item['isbn'], " ")+1);
                }
                if($item['isbn']) {
                    $book = BookModel::select('id')->where('isbn', $item['isbn'])->where('title', strip_tags($item['title']))->first();
                    if($book) {
                        $item['count'] = $book->count->toArray();
                    }
                }
                $returnData['channel']['item'] = [$item];
            } else {
                $returnData['channel']['item'] = [];
            }
        }
        else if($returnData['channel']['total'] > 1) {
            for($i=0,$cnt=count($returnData['channel']['item']); $i<$cnt; $i++) {
                $item = $returnData['channel']['item'][$i];

                if(gettype($item['isbn']) == 'array') $item['isbn'] = '';
                if(gettype($item['description']) == 'array') $item['description'] = '';
                if(gettype($item['image']) == 'array') $item['image'] = '';
                if(gettype($item['author']) == 'array') $item['author'] = '';
                if(gettype($item['publisher']) == 'array') $item['publisher'] = '';
                if(gettype($item['pubdate']) == 'array') $item['pubdate'] = '';

                $item['price'] = gettype($item['price']) == 'array' ? 0 : (int)$item['price'];
                $item['discount'] = gettype($item['discount']) == 'array' ? $item['price'] : (int)$item['discount'];

                if(strpos($item['author'], '|') !== false) {
                    $tmparr = explode('|', $item['author']);
                    $item['author'] = $tmparr[0].' 외 '.(count($tmparr)-1).'명';
                }

                if(strpos($item['isbn'], " ") !== false) {
                    $item['isbn'] = substr($item['isbn'], strpos($item['isbn'], " ")+1);
                }
                if($item['isbn']) {
                    $book = BookModel::select('id')->where('isbn', $item['isbn'])->where('title', strip_tags($item['title']))->first();
                    if($book) {
                        if(isset($book->count)) {
                            $item['count'] = $book->count->toArray();
                        } else {
                            $item['count'] = [];
                        }
                    }
                }
                $returnData['channel']['item'][$i] = $item;
            }
        }

        if (isset($returnData['channel']['item'])) {
            $returnData['channel']['item'] = collect($returnData['channel']['item'])->values();
        }

        return isset($returnData['channel']['item']) ? $returnData['channel']['item'] : null;
    }


}
