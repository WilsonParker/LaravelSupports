<?php


namespace LaravelSupports\Libraries\BookLibrary\Api;


use App\Library\LaravelSupports\app\Libraries\BookLibrary\Api\Response\NaverSearchBookResponse;
use GuzzleHttp\Client;

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
        $result = $response->getBody()->getContents();
        $model = new NaverSearchBookResponse();
        $model->bindJson($result);
        return $model;
    }
}
