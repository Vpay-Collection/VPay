layui.define(function(exports){ //提示：模块也可以依赖其它模块，如：layui.define('mod1', callback);
    const obj = {
        // 主题色orange_theme|blue_theme
        theme: "blue_theme",
        // 导航箭头ok-arrow2|ok-arrow3,不填为默认样式
        menuArrow: "ok-arrow2",
        //刷新后是否记住上次打开tab菜单
        isTabMenu: true,
        // 是否开启切换刷新
        isTabRefresh: false,
        baseUrl: "/admin/",
        debugBaseUrl:"/admin/",
        //debugBaseUrl: "https://www.fastmock.site/mock/4c635868c9b89cfb579b64d349c499ee/api",
        isDebug: true
    };

    if(localStorage.getItem("okConfig")===undefined||localStorage.getItem("okConfig")===null){
        localStorage.setItem("okConfig",JSON.stringify(obj));
        localStorage.setItem("skin",1)
        localStorage.setItem("anim","0")
    }
    exports('okConfig', obj);
});
