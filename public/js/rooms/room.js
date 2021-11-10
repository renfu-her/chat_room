$().ready(function () {
    // 加入退出狀況
    let onlineUsers = 0;
    let onlineClass = $('.online');
    let contentClass = $('.content');
    let typingClass = $('.typing');
    let sendBtn = $("#send");
    let content = $("#content");

    Echo.join('chat.' + room_id)
        .here((users) => {
            onlineUsers = users.length;
            onlineClass.text(onlineUsers);
        })
        .joining((user) => {
            onlineUsers++;
            onlineClass.text(onlineUsers);
            contentClass.append('<span class="joined">' + user.name + ' 加入了房间</span>');
            setTimeout("changeHeight()", 10);
        })
        .leaving((user) => {
            onlineUsers--;
            onlineClass.text(onlineUsers);
            contentClass.append('<span class="joined">' + user.name + ' 離開了房间</span>');
            setTimeout("changeHeight()", 10);
        })
        .listen('RoomMessageChannelEvent', (e) => {
            // 訊息
            let template = $("#template").html();
            let html;
            html = Mustache.render(template, {
                content: e.message.content,
                user_image: e.message.user.image,
                user_name: e.message.user.name,
                time: e.message.time,
                direction: e.message.user.id == user_id ? 'right' : 'left',
            });
            contentClass.append(html);
            //滚动到最下面
            setTimeout("changeHeight()", 10);
        })
        .listen('RoomMessageTypingChannelEvent', (e) => {
            let template = $("#typing_template").html();
            let html;
            if (e.message.type == 1) {
                html = Mustache.render(template, {
                    name: e.message.user.name,
                    user_id: e.message.user.id,
                });
            }
            typingClass.empty();
            typingClass.append(html);
        })
    ;

    // 傳送訊息
    sendBtn.click(function () {
        send();
    });

    //Enter
    $(document).keydown(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            if (e.shiftKey == 1) {
                let currVal = $("#content").val();
                $("#content").val(currVal += "\r\n");
            } else {
                send();
            }
        }
    });

    //一开始就滚动到最下面
    setTimeout("changeHeight()", 500);
});

//滚动
function changeHeight() {
    var beforeHeight = $(".content").scrollTop();
    $(".content").scrollTop($(".content").scrollTop() + 20);
    var afterHeight = $(".content").scrollTop();
    if (beforeHeight != afterHeight) {
        setTimeout("changeHeight()", 5);
    }
}

//Blur
function leftFocus() {
    typing(status_focus);
}

//Focus
function joinFocus() {
    typing(status_blur);
}

function typing(type) {
    $.post(typing_uri, {'type': type,}, function (res) {
    });
}

function send(){
    content = $("#content");
    if (content.val() == '') {
        alert('請填寫內容後再送出');
        content.focus();
        return false;
    }
    $.post(message_uri, {'_method': 'post', 'content': content.val()}, function (res) {
        if (res.status == false) {
            console.log(res);
            alert('發生錯誤');
        }
        // 清空
        content.val('');
        return false;
    });
}
