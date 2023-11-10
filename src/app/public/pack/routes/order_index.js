route("admin/order/index", {
    reference: "",
    depends:"admin/app/list?page=0&size=9999",
    container: "#container",
    title: "订单列表",
    onenter: function (query, dom, result) {
        var html = "";
        $.each(result.data,function (k,item) {
            html+=` <option value="${item.id}">${item.app_name}</option>`;
        });
        dom.find("#app").append(html);
    },
    onrender: function (query, dom, result) {
        let that = this;
        var config = {
            elem:"#datatable",
            url:'/admin/order/list',
            page:1,
            size:10,
            param:{
                "appid": $("#app").val(),
                "app_item": $("#form_name").val(),
                "status": $("#status").val(),
            },
            rowClick:function (data,config,response,raw) {
                mdbAdmin.modal.show({
                    title:'订单信息',
                    body:`
            <ul class="list-group list-group-light">
            <li class="list-group-item">${raw.state}<span class="me-2"></span>${raw.order_id}</li>
  <li class="list-group-item"><i class="fas fa-store me-2"></i>${raw.app_name}</li>
   <li class="list-group-item"><i class="fas fa-bag-shopping me-2"></i>${raw.app_item}</li>
   <li class="list-group-item"><i class="fas fa-clock me-2"></i>创建时间：${raw.create_time}</li>
    <li class="list-group-item"><i class="fas fa-clock me-2"></i>关闭时间：${mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", raw.close_time)}</li>
     <li class="list-group-item"><i class="fas fa-clock me-2"></i>支付时间：${mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", raw.pay_time)}</li>
  <li class="list-group-item"><i class="fas fa-database me-2"></i>参数：<pre>${JSON.stringify(JSON.parse(raw.param),null,2)}</pre></li>
</ul>
              `,
                    position: mdbAdmin.modal.position.center,
                    size: mdbAdmin.modal.size.default,
                    color: mdbAdmin.modal.color.primary,
                    buttons: [
                        ['关闭'],['好的']
                    ]
                });
            },
            onsuccess:function (data,config) {
                $(".edit-btn").off().on('click',function (e) {
                    e.stopPropagation();
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
                                    request("admin/order/callback", {order_id: json.order_id}).done(function () {
                                        that.db.reload({
                                            "appid": $("#app").val(),
                                            "app_item": $("#form_name").val(),
                                            "status": $("#status").val(),
                                        });
                                    });

                                }]
                        ],
                    });

                });
                $(".log-btn").off().on('click',function (e) {
                    e.stopPropagation();
                    var index = $(this).data("index");
                    var json = data[index];
                    request("admin/order/log", {order_id: json.order_id}).done(function (data) {
                        mdbAdmin.modal.show({
                            title:'回调日志',
                            body:data.msg,
                            color:mdbAdmin.modal.color.warning,
                            buttons: [
                                ['关闭'],
                                ['确定']
                            ],
                        });
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
                {label: '金额', field: 'price',render(row){
                        return `<i class="fas fa-yen-sign me-2"></i><span class="text-primary">${row.price}</span>`;
                    }},
                {label: '订单ID', field: 'order_id'},
                {label: '创建时间', field: 'create_time',render(row){
                        return mdbAdmin.dateFormat("yyyy-MM-dd hh:mm:ss", row.create_time);
                    }},
                {
                    label:"操作",
                    field: 'action',
                    fixed: 'right',
                    render(row,index){
                        var ret = "";
                        if(row.state!==3){
                            ret += `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-rotate"></i></button>
     `;
                        }

                        if(row.state===2){
                            ret += `
      <button class="log-btn btn btn-outline-primary btn-floating btn-sm"  data-index="${index}"><i class="fas fa-file-lines"></i></button>
     `;
                        }
                        return ret;

                    }
                }
            ],
        };
       that.db = mdbAdmin.database(config);
        $("#search").off().on("click", function () {

            that.db.reload({
                "appid": $("#app").val(),
                    "app_item": $("#form_name").val(),
                    "status": $("#status").val(),
            });
        });


    },
    onexit: function () {
        if (this.db) {
            this.db.destroy();
        }
    },
});
