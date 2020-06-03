<?php


namespace LaravelSupports\Libraries\Push\Events\Members\Abstracts;


use App\Models\Push\MemberAlim;
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
     * @param MemberAlim $notification
     */
    public function __construct(MemberAlim $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    /*public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }*/
}
