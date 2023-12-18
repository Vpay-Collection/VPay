router.init();
router.titlePrefix = "Vpay";

//进行路由
function replaceTpl(dom, data) {
    $.each(data, function (k, value) {
        dom.find("[data-name='" + k + "']").html(value).attr("src", value);
    });
}
