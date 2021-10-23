$().ready(function () {
    // 加入退出狀況
    let onlineUsers = 0;
    let onlineClass = $('.online');
    let contentClass = $('.content');
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
    // .listen('RoomMessageChannelEvent', (e) => {
    //     //
    //     console.log('listen');
    //     console.log(e);
    // })
    ;
    // 訊息
    window.Echo.channel('chat.' + room_id + '.message')
        .listen('RoomMessageChannelEvent', (e) => {
            let template = $("#template").html();
            let html;
            html = Mustache.render(template, {
                content: e.message.content,
                user_image: e.message.user.image,
                user_name: e.message.user.name,
                direction: e.message.user.id == user_id ? 'right' : 'left',
            });
            contentClass.append(html);
            //一开始就滚动到最下面
            setTimeout("changeHeight()", 10);
        });

    // 傳送訊息
    sendBtn.click(function () {
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
