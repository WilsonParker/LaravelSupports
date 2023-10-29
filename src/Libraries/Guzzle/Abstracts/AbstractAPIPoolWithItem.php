<?php


namespace LaravelSupports\Libraries\Guzzle\Abstracts;



use Exception;
use LaravelSupports\Libraries\Guzzle\Contracts\ApiPoolItem;

abstract class AbstractAPIPoolWithItem extends AbstractAPIPool
{
    protected function buildRequests($total)
    {
        foreach ($this->data as $key => $item) {
            throw_if(!$item instanceof ApiPoolItem, new Exception('Item must be an instance of ApiPoolItem'));
            yield $this->buildRequest($key, $item, $total);
        }
    }

    protected function buildRequest($key, $item, $total)
    {
        return $item->buildRequest($key, $item, $total);
    }

    protected function onFulfilled($response, $index)
    {
        $this->addApiPullResult($this->data[$index]->onFulfilled($response, $index));
    }
}
