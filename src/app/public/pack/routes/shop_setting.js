route("admin/shop/setting", {
    depends:"admin/shop/config",
    reference: "",
    container: "#container",
    title: "商城设置",
    onenter: function (query, dom, result) {
        dom.find("#wysiwygNotice").html(result.data.notice);
    },
    onrender: function (query, dom, result) {
        form.val("#shopSetting",result.data);
        form.submit("#shopSetting",function (data) {
            data.notice =  $(".wysiwyg-content").html();
            request("admin/shop/config",data).done(function (data) {
                mdbAdmin.toast.success(data.msg);
            });
            return false;
        });
        mdbAdmin.upload({
            elem: "#file-upload",
            url: '/admin/app/upload',
            dom: '', msg: '', onsuccess: function () {}
        });
    },
    onexit: function () {
    },
});
