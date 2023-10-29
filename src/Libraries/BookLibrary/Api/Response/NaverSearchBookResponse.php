<?php


namespace LaravelSupports\Libraries\BookLibrary\Api\Response;


use Illuminate\Support\Collection;
use LaravelSupports\Libraries\BookLibrary\Api\Response\Items\NaverSearchBookResponseItem;
use LaravelSupports\Libraries\Supports\Objects\Traits\ReflectionTrait;

class NaverSearchBookResponse
{
    use ReflectionTrait;

    public string $lastBuildDate = '';
    public int $total = 0;
    public int $start = 0;
    public int $display = 0;
    public int $perPage = 10;
    public $items = [];

    /**
     * @return string
     */
    public function getLastBuildDate(): string
    {
        return $this->lastBuildDate;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getDisplay(): int
    {
        return $this->display;
    }

    /**
     * @return array
     */
    public function getItems() : Collection
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function afterBind()
    {
        $this->items = collect($this->items)->map(function ($item) {
            $model = new NaverSearchBookResponseItem();
            $model->bindStd($item);
            return $model;
        });
    }

}
