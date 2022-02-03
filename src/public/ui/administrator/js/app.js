const $ = layui.$;
layui.config({
    base: '../static/js/lib/' //你存放新模块的目录，注意，不是 layui 的模块目录
}).extend({
    route: 'route',
    request: 'request',
    okConfig: 'okConfig',
    cookie: 'cookie',
    JSEncrypt: 'jsencrypt',
    loading: 'loading',
    NProgress: 'NProgress',
    okLayer: 'okLayer',
    tab: 'tab',
    utils: 'utils',
    cardTable: 'cardTable',
    clipboard: 'clipboard',
});
layui.okConfig =  {
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
            page: "console",
            callback: function (path) {
            }
        },
        {
            path: "",
            page: "console",
            callback: function (path) {
            }
        }
    ]
};
layui.use(['okConfig','route','request'], function(){
    //设置title
    layui.$("title").html(layui.okConfig.name)
    layui.$(".app_name").text(layui.okConfig.name)
    //设置路由
    if(window.location.hash.indexOf("#")===-1)
        window.location.hash="#/"
    const route = layui.route;
    layui.request.call("/index/user/isLogin","post",{},"#app").done(function (response){
            route.loadPage("home");
    }).fail(function (response) {
        if(response.data!==null)
            location.replace(response.data.url)
       else route.loadPage("login")
    });

});



