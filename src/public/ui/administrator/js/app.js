var $ = layui.$;
layui.config({
    base: './js/lib/' //你存放新模块的目录，注意，不是 layui 的模块目录
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
})
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
    }).fail(function () {
            route.loadPage("login")
    });

});



