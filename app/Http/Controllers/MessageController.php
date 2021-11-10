<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomJoin;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\RoomMessageChannelEvent;
use App\Events\RoomMessageTypingChannelEvent;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * @var Room
     */
    public $model;

    /**
     * @var RoomJoin
     */
    public $join;

    /**
     * @var Message
     */
    public $message;

    /**
     * RoomController constructor.
     *
     * @param  Room  $room
     * @param  RoomJoin  $join
     * @param  Message  $message
     */
    public function __construct(Room $room, RoomJoin $join, Message $message)
    {
        $this->model = $room;
        $this->join = $join;
        $this->message = $message;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     *
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 12:15
     */
    public function store(Request $request, $id)
    {
        if ($this->checkRoomAndUser($id) === false) {
            return response()->json([
                'status'       => false,
                'message'      => '房間狀態有誤',
                'redirect_uri' => route('room.chat', ['id' => $id]),
            ]);
        }
        $this->message->create(
            [
                'user_id' => Auth::user()->id,
                'room_id' => $id,
                'date'    => Carbon::now()->toDateTime(),
                'content' => $request->get('content'),
            ]
        );
        broadcast((new RoomMessageChannelEvent(['user' => Auth::user(), 'content' => $request->get('content')], $id)));
        return response()->json([
            'status'  => true,
            'message' => null,
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     *
     * @Author: Roy
     * @DateTime: 2021/10/28 上午 10:27
     */
    public function typing(Request $request, $id)
    {
        broadcast((new RoomMessageTypingChannelEvent(['user' => Auth::user(), 'type' => $request->get('type')],
            $id)))->toOthers();
        return response()->json([
            'status'  => true,
            'message' => null,
        ]);
    }

    /**
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 12:16
     */
    public function checkRoomAndUser($id)
    {
        $room = $this->checkAndGet($id);
        # 檢查房間狀態
        if (is_null($room) == true || (Auth::user()->id != $room->user_id && !$this->model->checkUserJoined($id,
                    $this->join))) {
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @param  string  $message
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/10/23 下午 12:17
     */
    public function checkAndGet($id, $message = '')
    {
        $table = $this->model->getTable();
        $config = config("status.{$table}");
        $info = $this->model->find($id);
        if (!$info || $info->status != $config['available']) {
            return null;
        }
        return $info;
    }
}
