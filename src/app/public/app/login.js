
mdbAdmin.useJs(['encrypt.min'],true).then(function () {
    $("form").on("submit", function () {
        $("#captcha_img").click();
        $("#captcha_code").val("");
        return false;
    });

    $("#submit_captcha").off().on("click", function () {
        mdbAdmin.request("/ankio/login/key",{},"GET",{"body":"加密中..."}).done(function (d) {
            const encrypt = new JSEncrypt();
            encrypt.setPublicKey(d.data);
            var passwd = $("input[name=password]").val();
            passwd = encrypt.encrypt(passwd);
            mdbAdmin.request("/ankio/login/login",{
                'code': $("#captcha_code").val(),
                'username': $("input[name=username]").val(),
                'password': passwd,
            },"POST",{"body":"登录中..."}).done(function () {
                location.reload();
            });

        });

    });
});


