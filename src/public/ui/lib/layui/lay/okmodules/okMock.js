"use strict";
layui.define([], function (exprots) {
    let okMock = {
        admin: {
            login:{
                isLogin:"login/isLogin",
                logout:"login/logout",
                passwd:"login/passwd",
                captcha:"login/captcha",
                publicKey:"login/publicKey"
            },
            nav:{
                list:"nav/list",
            }
            ,
            console:{
                data:"console/data",
            },
            setting:{
                passwd:'setting/passwd',
                qr:'setting/qr',
                order:'setting/order',
                app:'setting/app',
                mail:'setting/mail',
                mail_test:'setting/mail_test'
            },
            data:{
                qr:"data/qr",
                order:"data/order",
                app:"data/app",
                mail:'data/mail',
            },
            app:{
                list:"app/list",
                edit:"app/edit",
                del:"app/del"
            }

        }};

    exprots("okMock", okMock);
});
