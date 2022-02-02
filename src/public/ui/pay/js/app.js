const $ = layui.$;
layui.config({
    base: './js/lib/' //你存放新模块的目录，注意，不是 layui 的模块目录
}).extend({
    route: 'route',
    request: 'request',
    okConfig: 'okConfig',
    loading: 'loading',
    NProgress: 'NProgress',
    okLayer: 'okLayer',
})
layui.use(['okConfig','route','request'], function(){
    //设置title
    layui.$("title").html(layui.okConfig.name)
    layui.$(".app_name").text(layui.okConfig.name)
    //设置路由
    if(window.location.hash.indexOf("#")===-1)
        window.location.hash="#/"
    const route = layui.route;
    route.loadPage("home");
});



