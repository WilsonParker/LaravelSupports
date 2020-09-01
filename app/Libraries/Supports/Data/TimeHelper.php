<?php


namespace LaravelSupports\Libraries\Supports\Data;


use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimeHelper
{

    protected $channel = 'timer';
    private $callback;
    private $onCompleteCallback;
    private string $timeFormat = 'Y-m-d H:i:s';
    private string $executeTimeFormat = '%hh %im %s.%fs';
    private $startTime;
    private $endTime;
    private $executeTime;
    private $result;

    /**
     * TimeHelper constructor.
     *
     * @param $callback
     * @param $onCompleteCallback
     */
    public function __construct($callback, $onCompleteCallback = null)
    {
        $this->callback = $callback;
        $this->onCompleteCallback = $onCompleteCallback;
        /*$this->onCompleteCallback = function () {
            echo "시작 시간 : {$this->startTime->format($this->timeFormat)} <br/>";
            echo "종료 시간 : {$this->endTime->format($this->timeFormat)} <br/>";
            echo "소요 시간 : {$this->endTime->diff($this->startTime)->format($this->executeTime)} <br/>";
        };*/
    }

    protected function log()
    {
        Log::channel($this->channel)->info("시작 시간 : {$this->startTime->format($this->timeFormat)}");
        Log::channel($this->channel)->info("종료 시간 : {$this->endTime->format($this->timeFormat)}");
        Log::channel($this->channel)->info("소요 시간 : {$this->executeTime->format($this->executeTimeFormat)}");
    }

    /**
     *
     * @author  dew9163
     * @added   2020/02/27
     * @updated 2020/02/27
     * @updated 2020/09/01
     */
    public function execute()
    {
        $this->startTime = Carbon::now();
        $callback = $this->callback;
        $this->result = $callback();
        $this->endTime = Carbon::now();
        $this->executeTime = $this->endTime->diff($this->startTime);
        $this->log();
        if (isset($this->onCompleteCallback)) {
            $onCompleteCallback  =$this->onCompleteCallback;
            return $onCompleteCallback($this);
        }
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    /**
     * @return string
     */
    public function getExecuteTimeFormat(): string
    {
        return $this->executeTimeFormat;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return mixed
     */
    public function getExecuteTime()
    {
        return $this->executeTime;
    }

}
