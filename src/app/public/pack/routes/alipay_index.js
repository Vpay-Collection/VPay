route("admin/channel/index", {
  //  depends:"admin/channel/config",
    reference: "/",
    container: "#container",
    title: "支付宝配置",
    onenter: function (query, dom, result) {},
    onrender: function (query, dom, result) {

        form.init("#form_app","admin/channel/config");


    },
    onexit: function () {
    },
});
