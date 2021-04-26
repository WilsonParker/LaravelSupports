<?php


namespace App\Library\LaravelSupports\app\Libraries\BookLibrary\Api\Paginator;


use Illuminate\Pagination\LengthAwarePaginator;
use LaravelSupports\Libraries\BookLibrary\Api\Response\NaverSearchBookResponse;

class NaverBookApiPaginator extends LengthAwarePaginator
{
    protected $path = 'https://openapi.naver.com/v1/search/book_adv.json';
    protected $pageName = 'start';
    protected $perPage = 10;

    public function __construct(NaverSearchBookResponse $model, $options = [])
    {
        parent::__construct($model->getItems(), $model->getTotal(), $model->getPerPage(), $model->getStart(), $options);
    }

    public function previousPage()
    {
        if ($this->currentPage() > 1) {
            return $this->currentPage() - 1;
        }
    }

    public function nextPage()
    {
        if ($this->hasMorePages()) {
            return $this->currentPage() + 1;
        }
    }

    public static $defaultView = 'layouts.components.pagination.naver_book_api';
}
