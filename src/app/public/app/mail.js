$("form").off().on("submit", function () {
    var data = form.val("form");
    console.log(data);
    $.post("/api/admin/notice/config", data, function (data) {
        $("#success_msg_body").text(data.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
    });
    return false;
});

$("#test").off().on("click", function () {
    $("#testData").html("<p>测试中，请稍后...</p>");
    $.post("/api/admin/notice/test", {}, function (data) {
        $("#testData").html("<pre>" + data.data + "</pre>");
    });
});