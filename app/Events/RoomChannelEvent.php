<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Room;
use Illuminate\Support\Arr;

/**
 * Class RoomChannelEvent
 *
 * @package App\Events
 * @Author: Roy
 * @DateTime: 2021/10/21 下午 04:51
 */
class RoomChannelEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * RoomChannelEvent constructor.
     *
     * @Author: Roy
     * @DateTime: 2021/10/21 下午 04:51
     */
    public function __construct()
    {
        $this->message = (new Room())->get()->map(function ($RoomEntity) {
            return [
                'id'         => Arr::get($RoomEntity, 'id'),
                'title'      => Arr::get($RoomEntity, 'title'),
                'cover'      => asset(Arr::get($RoomEntity, 'cover',config('room.default_room_pic'))),
                'user_id'    => Arr::get($RoomEntity, 'user_id'),
                'is_private' => Arr::get($RoomEntity, 'is_private'),
                'cipher'     => Arr::get($RoomEntity, 'cipher'),
            ];
        })->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('RoomList');
    }
}
