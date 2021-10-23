$().ready(function () {
    $('input:radio').change(function () {
        if ($(this).val() == 1) {
            $('#cipherDiv').removeClass('hidden').addClass('show');
        } else {
            $('#cipherDiv').addClass('hidden');
        }
    });
});

