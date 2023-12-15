route("admin/shop/item", {
   // depends:"admin/shop/config",
    reference: "",
    container: "#container",
    title: "商品列表",
    onenter: function (query, dom, result) {},
    onrender: function (query, dom, result) {
        let that = this;
        var config = {
            elem:"#datatable",
            url:'/admin/shop/items',
            page:1,
            size:10,
            param:{
                'item': $("#form_name").val()
            },
            onsuccess:function (data,config) {
                $(".edit-btn").off().on('click',function (e) {
                    e.stopPropagation();
                    var index = $(this).data("index");
                    var json = data[index];

                    form.val("#form",json);
                    $(".wysiwyg-content").html(json["description"]);
                    $("#addApp").trigger("click");

                });
                $(".del-btn").off().on('click',function (e) {
                    e.stopPropagation();
                    var index = $(this).data("index");
                    var json = data[index];
                    mdbAdmin.modal.show({
                        title:'删除确认',
                        body:'确认删除<b>'+json.item+'</b>吗？',
                        color:mdbAdmin.modal.color.primary,
                        buttons: [
                            ['关闭'],
                            ['确定',
                                function () {
                                    request("admin/shop/delItem", {id:json.id}).done(function () {
                                        that.db.reload({
                                            'item': $("#form_name").val()
                                        });
                                    });

                                }]
                        ],
                    });

                });
            },
            columns:[
                { label: '状态', field: 'stop' ,render(row) {
                        var state = "";
                        switch (row.stop) {
                            case 1:
                                state = `<span class="badge badge-danger">停售</span>`;
                                break;
                            case 0:
                                state = `<span class="badge badge-success">在售</span>`;
                                break;
                        }
                        return state;
                    }},
                {label: '商品', field: 'item'},
                {label: '金额', field: 'price',render(row){
                        return `<i class="fas fa-yen-sign me-2"></i><span class="text-primary">${row.price}</span>`;
                    }},
                {label: '分类', field: 'category'},
                {label: 'WebHooK', field: 'api'},
                {
                    label:"操作",
                    field: 'action',
                    fixed: 'right',
                    render(row,index){
                        var ret = "";

                            ret += `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm me-2"  data-index="${index}"><i class="fas fa-pen"></i></button>
      <button class="del-btn btn btn-outline-danger btn-floating btn-sm"  data-index="${index}"><i class="fas fa-trash"></i></button>
     `;

                        return ret;

                    }
                }
            ],
        };
        that.db = mdbAdmin.database(config);
        $("#search").off().on("click", function () {

            that.db.reload({
                'item': $("#form_name").val()
            });
        });


        $("#saveOrUpdate").off("click").on("click", function () {
            var data = form.val("form");
            data["description"] =  $(".wysiwyg-content").html();
           request("admin/shop/addOrUpdateItem", data).done(function (data) {
               mdbAdmin.toast.success("添加/修改成功");
               $('[data-mdb-dismiss="modal"]').trigger('click');
               that.db.reload({
                   'item': $("#form_name").val()
               });
           });
        });
        $('#addOrUpdate').off('hidden.bs.modal').on('hidden.bs.modal', function () {
            form.reset("form");
            $(".wysiwyg-content").html('');
        });
        mdbAdmin.upload({
            elem: "#file-upload",
            url: '/admin/app/upload',
            dom: '', msg: '', onsuccess: function () {}
        });

    },
    onexit: function () {
        if (this.db) {
            this.db.destroy();
        }
    },
});
