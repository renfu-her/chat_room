<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RoomJoin extends Model
{
    /**
     * @var string
     */
    protected $table = 'room_join';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'created_at',
        'updated_at',
        'status'
    ];

    /**
     * @param $room_id
     * @return mixed
     */
    public function memberNum($room_id)
    {
        return $this->where([
            'room_id' => $room_id,
            'status' => 0
        ])->count();
    }

    /**
     * @param $roomId
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 03:56
     */
    public function checkUserJoined($roomId)
    {
        return $this
            ->where('user_id', Auth::user()->id)
            ->where('room_id', $roomId)
            ->exists();
    }
}
