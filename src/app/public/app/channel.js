mdbAdmin.use([mdbAdminPlugins["file-upload"]],true).then(function () {

    mdbAdmin.form.init("#form_app","/api/admin/channel/config");

    mdbAdmin.upload({
        elem: ".file-upload-input",
        url: '/api/admin/channel/upload',
        msg: '正在上传中...'
    });

});
