/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
(function () {
    mdbAdmin.form.init("form","/api/admin/notice/config",function (data) {
        var $mailArea = $("#mail_area");
        var $ssoArea = $("#sso_area");

        $("#sso").on("change", function () {
            // Toggle the visibility of mail_area and sso_area based on the checkbox's checked status
            $mailArea.toggle(!this.checked);
            $ssoArea.toggle(this.checked);
        });

        // Use ternary operator for showing/hiding elements based on data.sso value
        data.sso === 1 ? $ssoArea.show() : $mailArea.show();
        data.sso === 1 ? $mailArea.hide() : $ssoArea.hide();

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

