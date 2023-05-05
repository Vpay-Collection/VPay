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
        success: function (ret) {
            if (ret.code !== 200) {
                $("#error_msg_body").text(data.msg);
                mdb.Alert.getInstance(document.getElementById('error_msg')).show();
            } else {
                sessionStorage.setItem("image_" + id, ret.data);
            }
        }
    });
});
$("#saveOrUpdate").off().on("click", function () {
    var data = form.val("form");

    $.post("/api/user/app/addOrUpdate", data, function () {

    });
});