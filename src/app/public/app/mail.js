/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
(function () {
    mdbAdmin.form.init("form","/api/admin/notice/config",function (data) {
        $("#sso").on("change",function () {
            // Toggle the visibility of mail_area and sso_area based on the checkbox's checked status
            $("#mail_area").toggle(!this.checked);
            $("#sso_area").toggle(this.checked);
        });
        $("#mail_area").toggle(data.type===1);
        $("#sso_area").toggle(data.type!==1);
    });

    $("#test").off().on("click", function () {
        mdbAdmin.request( "/api/admin/notice/test",{},"POST",{"#app":"测试中..."}).done(function (data) {
            mdbAdmin.modal.show({
                'title':"测试结果",
                'body':data.data
            });
        });
    });
})();

