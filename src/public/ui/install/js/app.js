const $ = layui.$;
layui.config({
    base: '../static/js/lib/' //你存放新模块的目录，注意，不是 layui 的模块目录
}).extend({
    route: 'route',
    request: 'request',
    loading: 'loading',
    NProgress: 'NProgress',
    okLayer: 'okLayer',
});

layui.use(['route','request'], function(){
    //设置title
    layui.$("title").html("AnkioのVpay 安装")
    layui.$(".app_name").text("AnkioのVpay 安装")
    //设置路由
    if(window.location.hash.indexOf("#")===-1)
        window.location.hash="#/"
    const route = layui.route;
    layui.request.call("/index/install/isInstall","post",null,"#app","loading...").done(function (response) {
        route.loadPage("home");
    }).fail(function (response) {
       location.replace("/")
    });


});



