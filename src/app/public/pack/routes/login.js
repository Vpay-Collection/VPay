route("login", {
    js:["js/encrypt.min.js"],
    title:"登录",
    onenter: function (query, dom,result) {

        $.get("https://v1.hitokoto.cn/?c=i&encode=text",function (data) {
            dom.find("#sentence").text(data);
        });

        dom.find('#title').text("Vpay后台管理");
        request("ankio/login/islogin",{},null).done(function (data) {
            if(data.code===200){
                go("");
            }
        });
    },
    onrender: function (query, dom,result) {
        $("form").on("submit", function () {
            $("#captcha_img").trigger("click");
            $("#captcha_code").val("");
            return false;
        });

        $("#submit_captcha").off().on("click", function () {
            request("ankio/login/key",{},"加密中...",false).done(function (d) {
                const encrypt = new JSEncrypt();
                encrypt.setPublicKey(d.data);
                var passwd = $("input[name=password]").val();
                passwd = encrypt.encrypt(passwd);
              request("ankio/login/login",{
                    'code': $("#captcha_code").val(),
                    'username': $("input[name=username]").val(),
                    'password': passwd,
                },"登录中...",false).done(function (d) {
                   go("");
                });

            });

        });
    },
    onexit: function () {

    },
});
