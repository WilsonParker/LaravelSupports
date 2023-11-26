<?php

namespace LaravelSupports\Exceptions;

use Illuminate\Notifications\Notifiable;
use LaravelSupports\Exceptions\Loggers\Contracts\ExceptionServiceContract;
use LaravelSupports\Exceptions\Loggers\Contracts\Loggable;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class ExceptionSlackService implements ExceptionServiceContract
{
    use Notifiable;

    public function __construct(protected Loggable $logger) {}

    public function log(Throwable $throwable, array $options = []): void
    {
        try {
            $this->logger->log($throwable, $options);
            SlackAlert::to('error-logs')->message($this->getContent($throwable, $options));
        } catch (Throwable $throwable) {
            $this->logger->log($throwable);
            // notify 로 인해 발생한 throwable 처리
        }
    }

    private function getContent(Throwable $throwable, array $options = []): string
    {
        $name = $options['name'] ?? config('app.name');
        $file = $options['file'] ?? $throwable->getFile();
        $code = $options['code'] ?? $throwable->getCode();
        $line = $options['line'] ?? $throwable->getLine();
        $message = $options['message'] ?? $throwable->getMessage();
        return "
name : $name
date : " . now()->format('Y-m-d H:i:s') . "
file : $file
code : $code
line : $line
message : $message";
    }

    public function getCode(Throwable $throwable): int
    {
        return $this->logger->getCode($throwable);
    }

    public function routeNotificationForSlack($notification)
    {
        return config('exception.notifications.slack.webhook_url');
    }
}
