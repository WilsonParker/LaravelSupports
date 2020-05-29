<?php

namespace LaravelSupports\LibrariesPush\Listeners;

use App\Events\Members\MemberAlimNotificationEvent;
use App\Events\Members\MemberDetailNotificationEvent;
use App\Events\Members\MemberFeedNotificationEvent;
use App\Events\Members\MemberNotificationEvent;
use App\Services\Push\FCMPushService;
use Illuminate\Support\Arr;

class MemberEventSubscriber
{
    private FCMPushService $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = new FCMPushService();
    }

    public function handleMemberNotificationEvent($event)
    {
        $notification = $event->notification;
        $page = "AlimPage";
        $pageIdx = 0;

        $event1 = [
            'review-fly',
            'review-comment',
            'recom-comment',
            'recom-detail-fly',
            'recom-detail-comment',
            'follow',
        ];

        $event2 = [
            'meeting-join',
            'meeting-feed',
        ];

        $event3 = [
            'meeting-like',
            'meeting-comment',
        ];

        if (Arr::exists($event1, $notification->category)) {
            $page = "AlimPage";
        } else if (Arr::exists($event2, $notification->category)) {
            $page = "MeetingDetailPage";
            $pageIdx = $notification->page_idx;
        } else if (Arr::exists($event3, $notification->category)) {
            $page = "MeetingFeedPage";
            $pageIdx = $notification->page_idx;
        }

        $this->service->pushAll($notification->target_id, $notification->message, $page, $pageIdx);
    }

    /**
     *
     * App\Models\Push\MemberAlim->category
     * [
     *  review-fly
     *  review-comment
     *  recom-comment
     *  recom-detail-fly
     *  recom-detail-comment
     *  follow
     * ]
     *
     * @param
     * @return void
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    public function handleMemberAlimNotificationEvent($event)
    {
        $notification = $event->notification;
        $page = "AlimPage";
        $pageIdx = 0;
        $message = $notification->message;
        $this->service->pushAll($notification->target_id, $message, $page, $pageIdx);
    }

    /**
     * App\Models\Push\MemberAlim->category
     * [
     *  meeting-join
     *  meeting-feed
     * ]
     *
     * @param
     * @return
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    public function handleMemberDetailNotificationEvent($event)
    {
        $notification = $event->notification;
        $page = "MeetingDetailPage";
        $pageIdx = $notification->page_idx;
        $message = $notification->message;
        $this->service->pushAll($notification->target_id, $message, $page, $pageIdx);
    }

    /**
     * App\Models\Push\MemberAlim->category
     * [
     *  meeting-like
     *  meeting-comment
     * ]
     *
     * @param
     * @return
     * @author  dew9163
     * @added   2020/05/29
     * @updated 2020/05/29
     */
    public function handleMemberFeedNotificationEvent($event)
    {
        $notification = $event->notification;
        $page = "MeetingFeedPage";
        $pageIdx = $notification->page_idx;
        $message = $notification->message;
        $this->service->pushAll($notification->target_id, $message, $page, $pageIdx);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            MemberNotificationEvent::class,
            self::class . '@handleMemberNotificationEvent'
        );
        $events->listen(
            MemberAlimNotificationEvent::class,
            self::class . '@handleMemberAlimNotificationEvent'
        );

        $events->listen(
            MemberDetailNotificationEvent::class,
            self::class . '@handleMemberDetailNotificationEvent'
        );

        $events->listen(
            MemberFeedNotificationEvent::class,
            self::class . '@handleMemberFeedNotificationEvent'
        );
    }

}
