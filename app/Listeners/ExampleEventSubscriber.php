<?php

namespace LaravelSupports\Listeners;

use Illuminate\Database\Eloquent\Collection;
use LaravelSupports\Events\Abstracts\AbstractEvent;
use LaravelSupports\Libraries\Exceptions\Logs\ExceptionLogger;
use LaravelSupports\Listeners\Abstracts\AbstractEventSubscriber;

class ExampleEventSubscriber extends AbstractEventSubscriber
{
    /**
     * @var string[]
     */
    protected array $events = [
        AbstractEvent::class,
    ];

    public function handleEvent($event)
    {
        /*$meetingModel = $event->getMeetingModel();
        $memberModel = $event->getMemberModel();
        $data = $event->getData();

        if ($memberModel instanceof Collection) {
            $memberModel = $memberModel->filter(function ($item) {
                return Member::find($item->id);
            });
            foreach ($memberModel as $member) {
                $meetingModel->updateNotification($member->id, $data);
            }
        } else {
            $member = Member::find($memberModel->id);
            if (!is_null($member)) {
                $meetingModel->updateNotification($member->id, $data);
            }
        }*/
    }

    public function failed($event, \Exception $exception)
    {
        $logger = new ExceptionLogger();
        $logger->report($exception);
        $e = new \Exception($event->getErrorMessage());
        $logger->report($e);
        $this->handleException($event, $exception, $logger);
    }
}
