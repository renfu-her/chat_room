<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
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
     * @param  int  $room_id
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 03:58
     */
    public function getRoomMessage(int $room_id)
    {
        return $this
            ->with([
                'message'=>function($messageQuery){
                    return $messageQuery->with('user');
                },
                'room_join',
            ])
            ->whereHas('room_join',function ($query) use($room_id){
                return $query
                    ->where('room_id',$room_id)
                    ->where('user_id',Auth::id())
                ;
            })
            ->find($room_id)
        ;
    }
}
