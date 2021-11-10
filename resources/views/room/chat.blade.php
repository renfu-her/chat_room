@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{asset('css/chat.css')}}">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    {{--room start--}}
                    <div class="col-sm-12 col-md-12">
                        <div class="thumbnail">

                            <div class="caption">
                                {{--頭部--}}
                                <h4>{{$room->title}} <span class="pull-right number">(<span class="online"
                                        >0</span>/<span class="all">{{$memberNum}}</span>)
                                    </span></h4>
                                {{--内容--}}
                                <div class="content">
                                    @foreach($messages as $key => $dateMessages)
                                        <span class="joined">{{\Illuminate\Support\Carbon::today()->toDateString() == $key ? 'Today':$key}}</span>
                                        @foreach($dateMessages as $message)
                                        <div
                                            class="chat-{{$message->class}}">
                                            <img src="{{$message->user_image}}" alt="{{$message->user_name}}"
                                                 class="avatar pull-{{$message->class}}">
                                            <div
                                                class="pull-{{$message->class}}">
                                                <span class="username username-{{$message->class}}">{{$message->user_name}}</span>
                                                <br>
                                                <div>
                                                    <span class="content-span">{!!$message->content!!}</span>
                                                    <span class="timestamp-{{$message->class}}">{{$message->time}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        @endforeach
                                    @endforeach
                                </div>
                                {{--正在輸入--}}
                                <div class="typing"></div>
                                {{--底部--}}
                                <div class="form-group">
                                    <textarea onblur="leftFocus();" onfocus="joinFocus();" id="content"
                                              class="form-control wait-send" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary pull-right"
                                        role="button" id="send">送出
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    {{--room end--}}
                    <span class="default-value hide" data-default-avatar="{{config('room.default_avatar')}}"
                          data-user-id="{{Auth::user()->id}}" data-room-id="{{$room->id}}"></span>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/rooms/room.js?v='.config('app.version'))}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/3.0.0/mustache.min.js"></script>
    <script type="text/x-mustache" id="template">
    <div
        class="chat-@{{ direction }}">
        <img src="@{{ user_image }}" alt="@{{ user_name }}"
             class="avatar pull-@{{ direction }}">
        <div
            class="pull-@{{ direction }}">
            <span
                class="username username-@{{ direction }}">@{{ user_name }}</span>
            <br>
            <span class="content-span">@{{ content }}</span>
            <span class="timestamp-@{{ direction }}">@{{ time }}</span>
        </div>
    </div>
    <div class="clearfix"></div>
    </script>
    <script type="text/x-mustache" id="typing_template">
        <span id="typing">@{{ name }}正在輸入...</span>
    </script>
    <script>
        let user_id = {{Auth::user()->id}};
        let room_id = {{$room->id}};
        let message_uri = '{{route('room.message',['id'=>$room->id])}}';
        let typing_uri = '{{route('room.message.typing',['id'=>$room->id])}}';
        let status_blur = {{\App\Constants\TypingStatus::TypingStatus_Blur}};
        let status_focus = {{\App\Constants\TypingStatus::TypingStatus_Focus}};
    </script>
@endsection
