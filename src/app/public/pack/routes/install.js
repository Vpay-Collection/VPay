route("user/center/mine", {
    reference: "/",
    libs: ['file-upload'],
    js: [  'js/login-wechat.js',
        'js/login-finger.js'],
    depends: "user/center/get",
    container: "#container",
    title: "个人中心",
    onenter: function (query, dom, result) {
    },
    onrender: function (query, dom, result) {
   

    },
    onexit: function () {

    },
});
