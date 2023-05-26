$("#form_app").off().on("submit", function () {
    $.post("/api/admin/channel/config", form.val("#form_app"), function (data) {
        $("#success_msg_body").text(data.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
    });
    return false;
});

$(".file-upload-input").off().on('fileAdd.mdb.fileUpload', function (e) {
    const addedFile = e.files;
    const id = $(this).attr("id");
    const data = new FormData();
    data.append('file', addedFile[0]);
    $.ajax({
        type: 'POST',
        url: "/api/admin/channel/upload",
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend() {
            loading.show();
        },
        complete(){
            loading.hide();
        },
        success: function (ret) {
            if (ret.code !== 200) {
                $("#error_msg_body").text(ret.msg);
                mdb.Alert.getInstance(document.getElementById('error_msg')).show();
            } else {
                $("#" + id).data("image", ret.data);
                $("#success_msg_body").text("上传成功");
                mdb.Alert.getInstance(document.getElementById('success_msg')).show();
            }
        }
    });
});
$("#updateInfo").off().on("click", function () {
    $.post("/api/admin/channel/set", {
        "image_alipay": sessionStorage.getItem("image_alipay"),
        "image_wechat": sessionStorage.getItem("image_wechat"),
    }, function (data) {
        $("#success_msg_body").text(data.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
    });
});