$("#updateInfo").off().on("click", function () {
    var data = form.val("form");
    $.post("/ankio/login/change", data, function (d) {
        $("#success_msg_body").text(d.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
        location.reload();
    });
});
$(".file-upload-input").off().on('fileAdd.mdb.fileUpload', function (e) {
    const addedFile = e.files;
    const data = new FormData();
    data.append('file', addedFile[0]);
    $.ajax({
        type: 'POST',
        url: "/api/admin/user/upload",
        data: data,        beforeSend() {
            loading.show();
        },
        complete(){
            loading.hide();
        },
        cache: false,
        processData: false,
        contentType: false,
        success: function (ret) {
            if (ret.code !== 200) {
                $("#error_msg_body").text(ret.msg);
                mdb.Alert.getInstance(document.getElementById('error_msg')).show();
            } else {
                $("#success_msg_body").text("上传成功");
                mdb.Alert.getInstance(document.getElementById('success_msg')).show();
            }
        }
    });
});