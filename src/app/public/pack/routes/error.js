route("error", {
    title: "错误",
    onenter: function (query, dom,result) {
        var msg = sessionStorage.getItem("error");
        if(msg){
            dom.find("#title").text("错误");
            dom.find("#msg").text(msg);
        }
    },
    onrender: function (query, dom,result) {


    },
    onexit: function () {

    },
});
