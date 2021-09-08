<?php

namespace LaravelSupports\Listeners\Abstracts;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use LaravelSupports\Events\Abstracts\AbstractEvent;

abstract class AbstractEventSubscriber implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $tries = 2;
    protected string $handleMethod = 'handleEvent';
    protected string $listener;
    protected array $events;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->listener = get_class($this) . '@' . $this->handleMethod;
        $this->init();
    }

    protected function init()
    {
    }

    /**
     * handle when listen event
     *
     * @param AbstractEvent $event
     * @return void
     * @author  dew9163
     * @added   2020/08/12
     * @updated 2020/08/12
     * @updated 2021/01/14
     */
    /*public function handleEvent(AbstractEvent $event)
    {
        $event->handle();
    }*/
    abstract public function handleEvent($event);

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        foreach ($this->events as $event) {
            $events->listen($event, $this->listener);
        }
        $this->mergeSubscribe($events);
    }

    /**
     * Merge listeners for the subscribe
     *
     * @param $events
     * @return void
     * @author  dew9163
     * @added   2020/08/12
     * @updated 2020/08/12
     */
    protected function mergeSubscribe($events)
    {
    }


    /**
     * Exception handling when event failed
     *
     * @param $event
     * @param $exception
     * @param $logger
     * @return void
     * @author  dew9163
     * @added   2020/09/03
     * @updated 2020/09/03
     */
    protected function handleException($event, $exception, $logger)
    {

    }


}
