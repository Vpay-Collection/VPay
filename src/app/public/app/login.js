$("form").on("submit", function () {
    $("#captcha_img").click();
    $("#captcha_code").val("");
    return false;
});
$("#submit_captcha").off().on("click", function () {

    var submit = $("button[type=submit]");
    var text = submit.text();
    submit.html(`<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${text}`);
    submit.attr('disabled', 'true');
    $.get("/ankio/login/key", function (d) {
        const encrypt = new JSEncrypt();
        encrypt.setPublicKey(d.data);

        $.post(
            '/ankio/login/login',
            {
                'code': $("#code").val(),
                'username': $("input[name=username]").val(),
                'password': encrypt.encrypt($("input[name=password]").val()),
            }, function (data) {
                if (data.code === 200) {
                    console.log(data);
                    location.href = data.data;
                } else {
                    $("#error_msg_body").text(data.msg);
                    mdb.Alert.getInstance(document.getElementById('error_msg')).show();
                }
                submit.removeAttr('disabled');
                submit.text(text);
            }, 'json');
    }, 'json');


});