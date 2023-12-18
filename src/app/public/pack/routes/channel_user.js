route("admin/channel/user", {
  //  depends:"admin/channel/config",
    reference: "",
    container: "#container",
    title: "个人收款码",
    onenter: function (query, dom, result) {},
    onrender: function (query, dom, result) {
        let elem = $("#app_key");
        form.init("#form_app","admin/channel/app",function (data) {
            if(data.app_key!==""){
                $("#qrcode").attr("src","/api/image/qrcode?url="+encodeURIComponent(JSON.stringify({
                    'host':location.origin,
                    'key':data.app_key
                })));
            }
        });

        mdbAdmin.upload({
            elem: "[name='app_alipay']",
            url: '/admin/channel/upload',
            dom: '', msg: '', onsuccess: function () {}
        });
        mdbAdmin.upload({
            elem: "[name='app_wechat']",
            url: '/admin/channel/upload',
            dom: '', msg: '', onsuccess: function () {}
        });
    },
    onexit: function () {
    },
});
