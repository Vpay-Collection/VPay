/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
(function () {
    var config = {
        elem:"#datatable",
        url:'/api/admin/order/list',
        page:1,
        size:15,
        param:{
            "appid": $("#app").val(),
            "app_item": $("#form_name").val(),
            "status": $("#status").val(),
        },
        onsuccess:function (data,config) {
            $(".edit-btn").off().on('click',function () {
                var index = $(this).data("index");
                var json = data[index];

                mdbAdmin.modal.show({
                    title:'回调确认',
                    body:'确定对该订单进行回调吗？',
                    color:mdbAdmin.modal.color.primary,
                    buttons: [
                        ['关闭'],
                        ['确定',
                            function () {
                                mdbAdmin.request("/api/admin/order/callback", {order_id: json.order_id},"POST").done(function () {
                                    mdbAdmin.database(config);
                                });

                            }]
                    ],
                });

            });
        },
        columns:[
            { label: '状态', field: 'state' ,render(row) {
                    var state = "";
                    switch (row.state) {
                        case -1:
                            state = `<span class="badge badge-danger">已关闭</span>`;
                            break;
                        case 1:
                            state = `<span class="badge badge-primary">等待支付</span>`;
                            break;
                        case 2:
                            state = `<span class="badge badge-warning">已支付</span>`;
                            break;
                        case 3:
                            state = `<span class="badge badge-success">订单成功</span>`;
                            break;
                    }
                    return state;
                }},
            {label: '商户', field: 'app_name'},
            {label: '商品', field: 'app_item'},
            {label: '金额', field: 'real_price',render(row){
                if(row.real_price!==row.price){
                    return `<i class="fas fa-dollar-sign"></i><span class="text-primary">${row.real_price}</span><s><span class="text-primary">${row.danger}</span></s>`;
                }
                    return `<i class="fas fa-dollar-sign"></i><span class="text-primary">${row.real_price}</span>`;
                }},
            {label: '订单ID', field: 'order_id'},
            {label: '创建时间', field: 'create_time',render(row){
                    return mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", row.create_time);
                }},
            {label: '支付时间', field: 'pay_time',render(row){
                    return mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", row.pay_time);
                }},
            {label: '关闭时间', field: 'close_time',render(row){
                    return mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", row.close_time);
                }},
            {
                label:"操作",
                field: 'action',
                fixed: 'right',
                render(row,index){
                    if(row.state!==3){
                        return `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-rotate"></i></button>
     `;
                    }

                }
            }
        ],
    };
    mdbAdmin.database(config);
    $("#search").off().on("click", function () {
        mdbAdmin.database($.extend({},config,{
            param:{
                "appid": $("#app").val(),
                "app_item": $("#form_name").val(),
                "status": $("#status").val(),
            },
        }));
    });


})();



