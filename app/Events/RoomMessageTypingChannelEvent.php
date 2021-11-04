<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use App\Constants\TypingStatus;

/**
 * Class RoomMessageTypingChannelEvent
 *
 * @package App\Events
 * @Author: Roy
 * @DateTime: 2021/11/4 下午 04:14
 */
class RoomMessageTypingChannelEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $room_id;

    /**
     * RoomMessageTypingChannelEvent constructor.
     *
     * @param $message
     * @param $room_id
     *
     * @Author: Roy
     * @DateTime: 2021/10/28 上午 10:25
     */
    public function __construct($message, $room_id)
    {
        $this->room_id = $room_id;
        $this->message = (object) [
            'user' => (object) [
                'id'    => Arr::get($message, 'user.id'),
                'name'  => Arr::get($message, 'user.name'),
                'image' => sprintf("https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF",
                    Arr::get($message, 'user.name')),
            ],
            'type' => Arr::get($message, 'type',TypingStatus::TypingStatus_Blur),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel(sprintf("chat.%s", $this->room_id));
    }
}
