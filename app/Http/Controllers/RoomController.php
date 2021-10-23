<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoom;
use App\Models\Room;
use App\Models\RoomJoin;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Events\RoomChannelEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Events\RoomMessageChannelEvent;

class RoomController extends Controller
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Roy
     * @DateTime: 2021/10/22 下午 04:29
     */
    public function index()
    {
        $rooms = $this->model->paginate(config('room.page_size'));
        $paginate = $rooms->links('pagination.default');
        $rooms = $rooms->map(function ($room) {
            return (object) [
                'id'         => Arr::get($room, 'id'),
                'title'      => Arr::get($room, 'title'),
                'cover'      => asset(Arr::get($room, 'cover', config('room.default_room_pic'))),
                'user_id'    => Arr::get($room, 'user_id'),
                'is_private' => Arr::get($room, 'is_private'),
                'cipher'     => Arr::get($room, 'cipher'),
            ];
        });
        return view('room.lists', ['rooms' => $rooms, 'paginate' => $paginate]);
    }

    /**
     * 表单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('room.add');
    }

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
//        判断是否存在
        $roomEntity = $this->checkAndGet($request->id);
//        判断是否有权限
        if ($roomEntity->user_id != $request->user()->id) {
            abort(403, '无权操作');
        }
        $room = (object) [
            'id'         => Arr::get($roomEntity, 'id'),
            'title'      => Arr::get($roomEntity, 'title'),
            'cover'      => asset(Arr::get($roomEntity, 'cover', config('room.default_room_pic'))),
            'user_id'    => Arr::get($roomEntity, 'user_id'),
            'is_private' => Arr::get($roomEntity, 'is_private'),
            'cipher'     => Arr::get($roomEntity, 'cipher'),
        ];
        return view('room.edit', ['room' => $room]);
    }

    /**
     * @param  StoreRoom  $request
     *
     * @return mixed
     */
    public function store(StoreRoom $request)
    {
        $file = $request->file('cover');
        $data = [
            'title'      => $request->get('title'),
            'is_private' => $request->get('isPrivate'),
            'cipher'     => $request->get('cipher') ? bcrypt($request->get('cipher')) : '',
            'user_id'    => $request->user()->id,
        ];
        # 封面照
        if ($file) {
            $data['cover'] = sprintf('storage/%s', Storage::disk('public')->putFile(date('Y/m'), $file));
        }
        # 新增
        $roomEntity = $this->model->create($data);
        # room_join
        $roomEntity->room_join()->create(['room_id' => $roomEntity->id, 'user_id' => $request->user()->id]);

        event((new RoomChannelEvent()));
        return redirect(route('room.index'))->with('message', 'created success');
    }

    /**
     * @param  StoreRoom  $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreRoom $request, $id)
    {

        $file = $request->file('cover');
        $data = [
            'title'      => $request->get('title'),
            'is_private' => $request->get('isPrivate'),
            'cipher'     => $request->get('cipher') ? bcrypt($request->get('cipher')) : '',
            'user_id'    => $request->user()->id,
        ];
        # 封面照
        if ($file) {
            $data['cover'] = sprintf('storage/%s', Storage::disk('public')->putFile(date('Y/m'), $file));
        }
        $this->model->find($id)->update($data);
        event((new RoomChannelEvent()));
        return redirect(route('room.index'))->with('message', 'updated success');
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chat($id)
    {
//        判断房间是否存在
        $room = $this->checkAndGet($id);
//        判断用户是否加入
        if (Auth::user()->id != $room->user_id && !$this->model->checkUserJoined($id, $this->join)) {
            abort(403, '请先加入房间');
        }
        $latestMessages = $this->message->getLatestMessage($id, config('room.message_page_size'));
        foreach ($latestMessages as $message) {
            $message->content = nl2br($message->content);
            $message->user_image = sprintf("https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF",
                $message->user_name);
        }
//        获取圈子成员
        $memberNum = $this->join->memberNum($id);

        return view('room.chat', ['room' => $room, 'messages' => $latestMessages, 'memberNum' => $memberNum]);
    }

    /**
     * @param  Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function join(Request $request, $id)
    {
        $room = $this->checkAndGet($id);
        # 已加入
        if ($this->model->checkUserJoined($id, $this->join) || Auth::user()->id == $room->user_id) {
            return redirect(route('room.chat', ['id' => $id]));
        }
        # 密碼
        if ($room->isPrivate) {
            return redirect(route('room.index'))->with('modal', '请输入密码');
        }
        # 加入
        $this->join->create([
            'user_id' => Auth::user()->id,
            'room_id' => $id,
        ]);
        return redirect(route('room.chat', ['id' => $id]))->with('message', 'joined success');
    }

    /**
     * @param $id
     * @param  string  $message
     *
     * @return mixed
     * @Author: Roy
     * @DateTime: 2021/10/20 下午 05:01
     */
    public function checkAndGet($id, $message = '')
    {
        $table = $this->model->getTable();
        $config = config("status.{$table}");
        $info = $this->model->find($id);
        if (!$info || $info->status != $config['available']) {
            abort(404, $message ? $message : "This {$table} does not exist.");
        }
        return $info;
    }

    /**
     * @Author: Roy
     * @DateTime: 2021/10/22 下午 03:57
     */
    public function event()
    {
        event((new RoomMessageChannelEvent('test', 1)));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @Author: Roy
     * @DateTime: 2021/10/22 下午 04:26
     */
    public function joinRoom(Request $request, $id)
    {
        $room = $this->checkAndGet($id);
        # 已加入
        if ($this->model->checkUserJoined($id, $this->join) || Auth::user()->id == $room->user_id) {
            return response()->json([
                'status'       => true,
                'message'      => 'have joined',
                'redirect_uri' => route('room.chat', ['id' => $id]),
            ]);
        }
        # 密碼
        if ($room->is_private && !Hash::check($request->cipher, $room->cipher)) {
            return response()->json(['status' => false, 'message' => '密码不正确']);
        }

        $this->join->create([
            'user_id' => Auth::user()->id,
            'room_id' => $id,
        ]);

        return response()->json([
            'status'       => true,
            'message'      => 'joined success',
            'redirect_uri' => route('room.chat', ['id' => $id]),
        ]);
    }
}
