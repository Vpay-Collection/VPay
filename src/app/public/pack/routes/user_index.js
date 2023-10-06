route("admin/user/index", {
    libs:["file-upload"],
    reference: "/",
    depends:"admin/user/info",
    container: "#container",
    title: "个人中心",
    onenter: function (query, dom, result) {

    },
    onrender: function (query, dom, result) {
        form.val("form",result.data);
        form.bindSubmit("form","/ankio/login/change");

        mdbAdmin.upload({
            elem: ".file-upload-input",
            url: 'api/admin/user/upload',
            msg: '正在上传中...',
            onsuccess(data){
                $("#image").attr("src",data);
            }
        });
    },
    onexit: function () {

    },
});
