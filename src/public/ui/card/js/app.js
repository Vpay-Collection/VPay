const $ = layui.$;
layui.config({
    base: '../static/js/lib/' //你存放新模块的目录，注意，不是 layui 的模块目录
}).extend({
    route: 'route',
    request: 'request',
    loading: 'loading',
    NProgress: 'NProgress',
    okLayer: 'okLayer',
    cardTable: 'cardTable',
});

layui.okConfig =  {
    name:"Ankioの杂货铺",
    // 主题色orange_theme|blue_theme
    theme: "blue_theme",
    // 导航箭头ok-arrow2|ok-arrow3,不填为默认样式
    menuArrow: "ok-arrow2",
    baseUrl: "",
    isDebug: true,

};
layui.use(['route','request'], function(){
    //设置title
    layui.$("title").html(layui.okConfig.name)
    layui.$(".app_name").text(layui.okConfig.name)
    //设置路由
    if(window.location.hash.indexOf("#")===-1)
        window.location.hash="#/"
    const route = layui.route;
    route.loadPage("home");
});



