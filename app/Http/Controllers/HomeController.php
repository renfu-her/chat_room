<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Room;
use Illuminate\Support\Arr;
class HomeController extends Controller
{

    /**
     * @var
     */
    public $room;

    /**
     * HomeController constructor.
     *
     * @param  \App\Models\Room  $room
     *
     * @Author: Roy
     * @DateTime: 2021/10/20 下午 03:22
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Roy
     * @DateTime: 2021/10/20 下午 03:22
     */
    public function index()
    {
        $rooms = $this->room->paginate(config('room.page_size'));
        $paginate = $rooms->links('pagination.default');
        $rooms = $rooms->map(function ($room) {
            return (object)[
                'id'         => Arr::get($room, 'id'),
                'title'      => Arr::get($room, 'title'),
                'cover'      => asset(Arr::get($room, 'cover',config('room.default_room_pic'))),
                'user_id'    => Arr::get($room, 'user_id'),
                'is_private' => Arr::get($room, 'is_private'),
                'cipher'     => Arr::get($room, 'cipher'),
            ];
        });
        return view('home', ['rooms' => $rooms , 'paginate' =>$paginate]);
    }
}
