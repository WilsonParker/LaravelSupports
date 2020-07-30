<?php


namespace LaravelSupports\Libraries\Guzzle\Abstracts;


use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

abstract class AbstractAPIPool
{
    protected $totalCount;
    protected $counter = 1;
    protected $concurrency = 10; // concurrently crawling
    protected $client;
    protected $data;
    protected $apiPoolResult = [];
    protected $result;

    /**
     * AbstractAPIPool constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->client = new Client();
    }

    protected function init()
    {
        $this->counter = 1;
        $this->totalCount = count($this->data);
    }

    public function handle()
    {
        $this->init();

        $pool = new Pool($this->client, $this->buildRequests($this->totalCount), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                $this->onFulfilled($response, $index);
                $this->countedAndCheckEnded();
            },
            'rejected' => function ($reason, $index) {
                $this->onRejected($reason, $index);
                $this->countedAndCheckEnded();
            },
        ]);

        // start sending requests
        $promise = $pool->promise();
        $promise->wait();
    }

    /**
     * @param int $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @param int $concurrency
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getApiPoolResult()
    {
        return $this->apiPoolResult;
    }

    protected function addApiPullResult($data)
    {
        array_push($this->apiPoolResult, $data);
    }

    protected function buildRequests($total)
    {
        foreach ($this->data as $key => $item) {
            yield $this->buildRequest($key, $item, $total);
        }
    }

    abstract protected function buildRequest($key, $item, $total);

    abstract protected function onFulfilled($response, $index);

    abstract protected function onComplete();

    abstract protected function onRejected($reason, $index);

    protected function countedAndCheckEnded()
    {
        if ($this->counter < $this->totalCount) {
            $this->counter++;
            return;
        }
        $this->onComplete();
    }
}
