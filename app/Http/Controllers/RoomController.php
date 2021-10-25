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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Roy
     * @DateTime: 2021/10/24 下午 11:18
     */
    public function create()
    {
        return view('room.add');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Roy
     * @DateTime: 2021/10/24 下午 11:18
     */
    public function edit(Request $request)
    {
        # 判断是否存在
        $roomEntity = $this->checkAndGet($request->id);
        # 判断是否有权限
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
     * @param  \App\Http\Requests\StoreRoom  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author: Roy
     * @DateTime: 2021/10/24 下午 11:18
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
     * @param  \App\Http\Requests\StoreRoom  $request
     * @param $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author: Roy
     * @DateTime: 2021/10/24 下午 11:18
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Roy
     * @DateTime: 2021/10/24 下午 11:18
     */

    public function chat($id)
    {
        # 判斷房間是否存在
        $room = $this->checkAndGet($id);
        # 判斷用戶是否加入
        if (Auth::user()->id != $room->user_id && !$this->model->checkUserJoined($id, $this->join)) {
            abort(403, '請先加入房間');
        }
        $latestMessages = $this->message->getLatestMessage($id, config('room.message_page_size'));
        foreach ($latestMessages as $message) {
            $message->content = nl2br($message->content);
            $message->user_image = sprintf("https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF",
                $message->user_name);
        }
        # 獲取圈子成員
        $memberNum = $this->join->memberNum($id);

        return view('room.chat', ['room' => $room, 'messages' => $latestMessages, 'memberNum' => $memberNum]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author: Roy
     * @DateTime: 2021/10/25 上午 10:07
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
     * @DateTime: 2021/10/24 下午 11:17
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
            return response()->json(['status' => false, 'message' => '密碼不正確']);
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
