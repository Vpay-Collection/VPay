route("admin/app/index", {
    reference: "",
    container: "#container",
    title: "App管理",
    onenter: function (query, dom, result) {

    },
    onrender: function (query, dom, result) {
        let that = this;
        var frameIndex = function (title,data,config) {
            loadFrame(title,"admin/app/edit",{
                data:data,
                db:that.db
            });
        };
        var config = {
            elem:"#datatable",
            url:'/admin/app/list',
            page:1,
            size:15,
            onsuccess:function (data,config) {
                mdbAdmin.initComponents("#datatable");
                $('.clipboard').off().on('copy.mdb.clipboard', function (e) {
                    mdbAdmin.toast.success(e.copyText+"已复制","剪切板");
                });


                $(".edit-btn").off().on('click',function () {
                    var index = $(this).data("index");
                    var json = data[index];
                    frameIndex("编辑应用",json,config);
                });
                $(".delete-btn").off().on('click',function () {
                    var index = $(this).data("index");
                    var json = data[index];

                    mdbAdmin.modal.show({
                        title:'删除确认',
                        body:'确认删除<b>'+json.app_name+'</b>吗？',
                        color:mdbAdmin.modal.color.primary,
                        buttons: [
                            ['关闭'],
                            ['确定',
                                function () {
                                    request("admin/app/del", {id:json.id}).done(function () {
                                        that.db.reload();
                                    });

                                }]
                        ],
                    });

                });
            },
            columns:[
                { label: 'Logo', field: 'app_image',width:100 ,render(row) {
                        return `<img
  src="${row.app_image}"
  class="img-fluid rounded-circle"
  alt=""  height="fit-content" style="height: 50px;width: 50px"
/>`;
                    }},
                { label: '网站', field: 'app_name'
                },
                { label: 'AppId', field: 'id' ,render(row,index) {
                        return  `<span data-mdb-clipboard-target=".clipboard-appid-${index}"  class="clipboard clipboard-appid-${index}"  data-mdb-clipboard-text="${row.id}">${row.id}</span>`;
                    } },
                { label: 'SecretKey', field: 'app_key' ,render(row,index) {
                        return  `<span data-mdb-clipboard-target=".clipboard-secret_key-${index}"
 class="clipboard clipboard-secret_key-${index}" data-mdb-clipboard-text="${row.app_key}">${row.app_key}</span>`;
                    }},
                {
                    label:"操作",
                    field: 'action',
                    fixed: 'right',
                    render(row,index){
                        return `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-pen"></i></button>
      <button class="delete-btn btn ms-2 btn-primary btn-floating btn-sm" data-index="${index}"><i class="fa fa-trash"></i></button>`;
                    }
                }
            ],
        };
       this.db = mdbAdmin.database(config);
        $("#addApp").on("click",function () {
            frameIndex("新增App",null,config);
        });
    },
    onexit: function () {
        if (this.db) {
            this.db.destroy();
        }
    },
});
