<?php


namespace LaravelSupports\Libraries\Push\Events\Members\Abstracts;


use App\Models\Push\MemberAlimModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbstractMemberEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * AbstractMemberEvent constructor.
     *
     * @param MemberAlimModel $notification
     */
    public function __construct(MemberAlimModel $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    /*public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }*/
}
