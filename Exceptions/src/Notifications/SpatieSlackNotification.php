<?php

namespace LaravelSupports\Exceptions\Notifications;


use Spatie\SlackAlerts\Jobs\SendToSlackChannelJob;

class SpatieSlackNotification extends SendToSlackChannelJob
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    public $queue = 'exception-slack';
}
