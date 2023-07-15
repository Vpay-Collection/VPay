(function () {
  form.submit("form",function (data) {
     mdbAdmin.request('//api/index/main/install',data).done(function () {
        $("#setting .btn-close").click();
        mdbAdmin.modal.show({
           title:'安装成功',
           body:`
           您的登录账号：${data['_username']} <br>
           您的登录密码：${data['_password']}
           `,
           color:mdbAdmin.modal.color.success,
           buttons: [
              ['关闭'], ['访问后台',function () {
                 location.href = "/admin";
              }]
           ],
        });
     });
  });
})();
