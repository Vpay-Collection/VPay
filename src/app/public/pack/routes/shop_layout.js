route("shop", {
    title: "",
    depends: "index/shop/config",
    onenter: function (query, dom, result) {
        if (result.data.state!==1) {
            location.href = "/";
            return true;
        }
        replaceTpl(dom,result.data);
        if (window.location.pathname === "/@shop" || window.location.pathname === "/@shop/") {
            go("shop/index");
        }
    },
    onrender: function () {


    },
    onexit: function () {

    },
});
