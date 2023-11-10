frame("admin/app/edit", {
    libs:["file-upload"],
    config:null,
    onenter: function (query, dom,result) {

    },
    onrender: function (query, dom,result) {
        this.db = query.db;
        mdbAdmin.upload({
            elem: "#file-upload",
            url: '/admin/app/upload',
            dom: '', msg: '', onsuccess: function () {}
        });
        form.submit("#form", function (data) {
            request("admin/app/addOrUpdate", data,  "数据提交中...").done(function (data) {
                if(data.code!==200){
                    mdbAdmin.toast.error(data.msg);
                }else{
                    $("[data-mdb-dismiss]").trigger("click");
                }
            });
        });
        if(query.data){
            form.val("#form",query.data);
        }
    },
    onexit: function () {
        var instance = FileUpload.getInstance(document.getElementById("file-upload"));
        if(instance){
            instance.dispose();
        }
      this.db.reload();
    },
});
