<?php


use LaravelSupports\Exceptions\Loggers\DatabaseLogger;

return [
    'logger' => DatabaseLogger::class,
    'model' => Exception::class,

    'notifications' => [
        'slack' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel' => 'error-logs', // Replace with your desired Slack channel
        ],
    ],
];
