/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

(function () {
    var config = {
        elem:"#datatable",
        url:'/api/admin/shop/category',
        page:1,
        size:15,
        onsuccess:function (data,config) {
            $(".edit-btn").off().on('click',function () {
                var index = $(this).data("index");
                var json = data[index];
                form.val("#form",json);

                $("#addApp").click();
            });
            $(".delete-btn").off().on('click',function () {
                var index = $(this).data("index");
                var json = data[index];

                mdbAdmin.modal.show({
                    title:'删除确认',
                    body:'确认删除<b>'+json.name+'</b>吗？',
                    color:mdbAdmin.modal.color.primary,
                    buttons: [
                        ['关闭'],
                        ['确定',
                            function () {
                                mdbAdmin.request("/api/admin/shop/delCategory", {id:json.id},"POST").done(function () {
                                    mdbAdmin.database(config);
                                });

                            }]
                    ],
                });

            });
        },
        columns:[
            {label: 'ID', field: 'id'},
            {label: '分类', field: 'name'},
            {
                label:"操作",
                field: 'action',
                fixed: 'right',
                render(row,index){
                    if(row.id===1){
                        return `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-pen"></i></button>
   `;
                    }
                    return `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-pen"></i></button>
      <button class="delete-btn btn ms-2 btn-primary btn-floating btn-sm" data-index="${index}"><i class="fa fa-trash"></i></button>`;
                }
            }
        ],
    };
    mdbAdmin.database(config);
    form.submit("form",function (data) {
        mdbAdmin.request("/api/admin/shop/addOrUpdateCategory", data).done(function (data) {
           mdbAdmin.toast.success(data.msg);
            mdbAdmin.database(config);
            $("[data-mdb-dismiss]").click();
        });
    });
    $('#addOrUpdate').off().on('hidden.bs.modal', function () {
        form.reset("#form");
    });
})();



