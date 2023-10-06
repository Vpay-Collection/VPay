/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

route("install", {
    title: "应用安装",
    depends:"index/install/info",
    onenter: function (query, dom, result) {
        if(result.data.install){
           go("login");
        }
        if(result.data.docker){
            dom.find("#isDocker").hide();
        }
        
    },
    onrender: function (query, dom, result) {
        form.submit("form",function (data) {
            request("index/install/start",data).done(function (d) {
                mdbAdmin.modal.show({
                    title:'安装成功',
                    body:'后台账号：'+data['_username']+"<br>"+'后台密码：'+data['_password'],
                    color:mdbAdmin.modal.color.success,
                    onclose:function () {
                        go("login");
                    },
                });
            });
        });
    },
    onexit: function () {

    },
});
