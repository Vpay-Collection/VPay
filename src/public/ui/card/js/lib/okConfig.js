layui.define(function(exports){ //提示：模块也可以依赖其它模块，如：layui.define('mod1', callback);
    const obj = {
        name:"AnkioのVpay",
        // 主题色orange_theme|blue_theme
        theme: "blue_theme",
        // 导航箭头ok-arrow2|ok-arrow3,不填为默认样式
        menuArrow: "ok-arrow2",
        baseUrl: "",
        isDebug: true,
        routes:[
            {
                path: "/",
                page: "index",
                callback: function (path) {
                }
            },
            {
                path: "",
                page: "index",
                callback: function (path) {
                }
            }
        ]
    };

    exports('okConfig', obj);
});
