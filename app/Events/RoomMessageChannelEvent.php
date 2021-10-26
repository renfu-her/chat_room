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

/**
 * Class RoomMessageChannelEvent
 *
 * @package App\Events\Rooms
 * @Author: Roy
 * @DateTime: 2021/10/21 下午 02:34
 */
class RoomMessageChannelEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $room_id;

    /**
     * RoomMessage constructor.
     *
     * @param $message
     * @param $room_id
     *
     * @Author: Roy
     * @DateTime: 2021/10/21 上午 11:54
     */
    public function __construct($message, $room_id)
    {
        $this->room_id = $room_id;
        $this->message = (object) [
            'user'    => (object) [
                'id'    => Arr::get($message, 'user.id'),
                'name'  => Arr::get($message, 'user.name'),
                'image' => sprintf("https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF",
                    Arr::get($message, 'user.name')),
            ],
            'content' => Arr::get($message, 'content'),
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
