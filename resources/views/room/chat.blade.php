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
                                {{--头部--}}
                                <h4>{{$room->title}} <span class="pull-right number">(<span class="online"
                                        >0</span>/<span class="all">{{$memberNum}}</span>)
                                    </span></h4>
                                {{--内容--}}
                                <div class="content">
                                    @foreach($messages as $message)
                                        <div
                                            class="{{Auth::user()->id == $message->user_id ? 'chat-right' : 'chat-left'}}">
                                            <img src="{{$message->user_image}}" alt="{{$message->user_name}}"
                                                 class="avatar pull-{{Auth::user()->id == $message->user_id ? 'right' : 'left'}}">
                                            <div
                                                class="{{Auth::user()->id == $message->user_id ? 'pull-right' : 'pull-left'}}">
                                                <span
                                                    class="username username-{{Auth::user()->id == $message->user_id ? 'right' : 'left'}}">{{$message->user_name}}</span>
                                                <br>
                                                {{--防止换行符被转字符串--}}
                                                <div class="content-span">{!!$message->content!!}</div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    @endforeach
                                </div>
                                {{--底部--}}
                                <div class="form-group">
                                    <textarea id="content" class="form-control wait-send" rows="3"></textarea>
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
    <script src="{{asset('js/rooms/room.js'.config('app.version'))}}"></script>
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
            <div class="content-span">@{{ content }}</div>
        </div>
    </div>
    <div class="clearfix"></div>

    </script>
    <script>
        let user_id = {{Auth::user()->id}};
        let room_id = {{$room->id}};
        let message_uri = '{{route('room.message',['id'=>$room->id])}}';
    </script>
@endsection
