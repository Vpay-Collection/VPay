/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
mdbAdmin.use([mdbAdminPlugins["file-upload"]],true).then(function () {

    mdbAdmin.form.bindSubmit("#form_app","/ankio/login/change");

    mdbAdmin.upload({
        elem: ".file-upload-input",
        url: '/api/admin/user/upload',
        msg: '正在上传中...',
        onsuccess(data){
            $("#image").attr("src",data);
        }
    });

});