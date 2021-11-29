<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;
    /**
     * @var string
     */
    protected $table = 'message';

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
        'status',
        'content',
        'date',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 02:44
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 02:44
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $room_id
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 02:44
     */
    public function getLatestMessage($room_id)
    {
        return $this->leftJoin('users' , 'message.user_id' , '=' , 'users.id')
            ->select('message.content' , 'message.created_at' , 'users.id as user_id' , 'users.name as user_name','message.date')
            ->where('message.room_id' , '=' , $room_id)
            ->where('message.status' , '=' , config('status.message.available'))
            ->get()
            ->groupBy('date');
    }
    /**
     * @param $room_id
     *
     * @Author: Roy
     * @DateTime: 2021/11/29 下午 02:44
     */
    public function deleteMessage($room_id)
    {
        return $this->where('room_id',$room_id)->delete();
    }
}
