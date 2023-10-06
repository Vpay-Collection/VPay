$(window).on("hashchange", router);
var loadingIndex = 19999999;
router({
    oldURL: null,
    newURL: location.href,
});
function replaceTpl(dom,data) {
    $.each(data,function (k,value) {
        dom.find("[data-name='"+k+"']").text(value);
    });
}

titlePrefix = "Vpayの个人收款平台";