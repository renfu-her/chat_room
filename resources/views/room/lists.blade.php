@extends('layouts.app')
@section('content')
    <style>
        .row .thumbnail img {
            height: 150px;
            cursor: pointer;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="row" id="room_list">
                    {{--room start--}}
                    @foreach($rooms as $room)
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <a href="#" target="_blank" >
                                    <img class="room_img" data-id="{{$room->id}}"
                                        src="{{is_null($room->cover) ? asset(config('room.default_room_pic')) : asset($room->cover)}}"
                                        alt="{{$room->title}}">
                                </a>
                                <div class="caption">
                                    <h4>{{$room->title}}</h4>
                                    <p>
                                        @if($room->user_id == Auth::user()->id)
                                            <a href="{{url("room/$room->id/edit")}}" class="btn btn-primary"
                                               role="button">編輯</a>
                                        @endif
                                        <a href="#" data-id="{{$room->id}}" data-private="{{$room->is_private}}" data-join="{{$room->actions->join_uri}}" class="btn btn-primary join room_{{$room->id}}"
                                           role="button">加入</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{--room end--}}
                </div>
            </div>
        </div>
    </div>
    {{ $paginate }}

    <div class="modal" id="joinModal" tabindex="-1" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close closeBtn" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">請輸入密碼</h4>
                </div>
                <div class="modal-body">
                    <form>
                        @method('put')
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">密碼:</label>
                            <input type="text" id="cipher" class="form-control" id="recipient-name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default closeBtn" data-dismiss="modal">關閉</button>
                    <button type="button" class="btn btn-primary" id="confirm" data-container="body"
                            data-toggle="popover" data-placement="bottom" data-content="密碼錯誤">加入
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/3.0.0/mustache.min.js"></script>
    <script type="text/x-mustache" id="template">
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            <a target="_blank" href="@{{ room_id }}">
                <img src="@{{ cover }}"
                     alt="@{{ title }}">
            </a>
            <div class="caption">
                <h4>@{{ title }}</h4>
                <p>
                    <button data-id="@{{ room_id }}" data-private="@{{ is_private }}"
                            class="btn btn-primary join">加入
                    </button>
                </p>
            </div>
        </div>
    </div>
    </script>
    <script src="{{asset('js/rooms/index.js?v='.config('app.version'))}}"></script>
@endsection

