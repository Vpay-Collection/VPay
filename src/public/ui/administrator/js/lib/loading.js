"use strict";
layui.define(['jquery','NProgress'], function (exports) {
   layui.link("./css/okLoading.css");
    const $ = layui.$;
    const NProgress = layui.NProgress;
    let obj={
        close: function (dom) {
            if(dom===undefined||dom===null)return
            $(dom).find(".ok-loading").fadeTo("slow",0.01,function () {
                $(this).slideUp("slow",function () {
                    $(this).remove();
                    NProgress.done();
                })
            });
        },
        show:function (str,dom) {
            if(dom===undefined||dom===null)return;
            NProgress.settings.parent=dom;
            NProgress.start();
           // console.log(str)
          str = str||"Loading...";
            dom = dom||"#app"
            $(dom).append("<div class='ok-loading'>" +
                "<div class='ball-loader'>" +
                "<span></span><span></span><span></span><span></span>" +
                "<div style='text-align: center;margin-top: 0.5rem;color: white'>"+str+"</div></div>"+
                "</div>");
        }
    }
    exports("loading", obj);
});