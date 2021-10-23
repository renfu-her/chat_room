<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Room extends Model
{
    /**
     * @var string
     */
    protected $table = 'room';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'created_at',
        'updated_at',
        'is_private',
        'cipher',
        'cover',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 02:03
     */
    public function message()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 02:03
     */
    public function room_join()
    {
        return $this->hasMany(RoomJoin::class, 'room_id', 'id');
    }

    /**
     * @return \App\Models\RoomJoin
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 02:32
     */
    private function getEntity(): RoomJoin
    {
        if (app()->has(RoomJoin::class) === false) {
            app()->singleton(RoomJoin::class);
        }

        return app(RoomJoin::class);
    }

    /**
     * @param $roomId
     * @param  \App\Models\RoomJoin  $join
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 02:31
     */
    public function checkUserJoined($roomId, RoomJoin $join)
    {
        return $this->getEntity()
            ->where('status', config('status.room_join.available'))
            ->where('user_id', Auth::user()->id)
            ->where('room_id', $roomId)
            ->exists();
    }
}
