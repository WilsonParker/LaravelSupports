<?php


namespace LaravelSupports\Libraries\BookLibrary\Api\Response;


use App\Library\LaravelSupports\app\Libraries\BookLibrary\Api\Response\Items\NaverSearchBookResponseItem;
use LaravelSupports\Libraries\Supports\Objects\Traits\ReflectionTrait;

class NaverSearchBookResponse
{
    use ReflectionTrait;

    public string $lastBuildDate = '';
    public int $total = 0;
    public int $start = 0;
    public int $display = 0;
    public $items = [];

    public function afterBind()
    {
        $this->items = collect($this->items)->map(function ($item) {
            $model = new NaverSearchBookResponseItem();
            $model->bindArray($item);
            return $model;
        });
    }

}
