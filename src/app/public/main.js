router.init();
router.titlePrefix = "Vpay";

//进行路由
function replaceTpl(dom, data) {
    $.each(data, function (k, value) {
        dom.find("[data-name='" + k + "']").text(value).attr("src", value);
    });
}
