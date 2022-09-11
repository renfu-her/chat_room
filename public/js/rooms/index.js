let id;
let token = $('meta[name="csrf-token"]').attr('content');
let href;
$().ready(function () {
    window.Echo.channel('RoomList')
        .listen('RoomChannelEvent', (e) => {
            ajaxLoadingOpen();
            let room_list = $("#room_list");
            let template = $("#template").html();
            let html = '';
            for (let i = 0; i < e.message.length; i++) {
                html += Mustache.render(template, {
                    room_id: e.message[i].id,
                    title: e.message[i].title,
                    cover: e.message[i].cover,
                    is_private: e.message[i].is_private
                });
            }
            room_list.empty();
            room_list.append(html);
            ajaxLoadingClose();
            // 重新監聽
            join();
        });
    //監聽
    join();
    $('#confirm').click(function () {
        ajaxLoadingOpen();
        $.post(herf, {
            '_method': 'put',
            '_token': token,
            'cipher': $('#cipher').val()
        }, function (res) {
            if (res.status == true) {
                location.href = res.redirect_uri;
            } else {
                alert(res.message);
                $("#recipient-name").val('');
            }
        });
        ajaxLoadingClose();
    });
    $(".room_img").on('click', function () {
        $('.room_' + $(this).data('id')).trigger('click');
        return false;
    });
    $('#joinModal').on('hidden.bs.modal', function (e) {
        $('#confirm').popover('hide');
        return true;
    });
    $('.closeBtn').click(function () {
        $('#joinModal').modal('hide');
        return true;
    });
});

function join() {
    $('.join').click(function () {
        $('#test').modal('show');
        id = $(this).data('id');
        herf = $(this).data('join');
        if ($(this).data('private') == 1) {
            $("#recipient-name").val('');
            $('#joinModal').modal('show');
        } else {
            ajaxLoadingOpen();
            $.post(herf, {'_method': 'put', '_token': token}, function (res) {
                if (res.status == true) {
                    location.href = res.redirect_uri;
                } else {
                    alert(res.message);
                }
            });
            ajaxLoadingClose();
        }
        return false;
    });
}
