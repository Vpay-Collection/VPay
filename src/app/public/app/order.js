/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

function getTable() {
    return mdb.Datatable.getOrCreateInstance(document.getElementById('datatable'));
}

function loadTable(page, size) {
    $.post('/api/admin/order/list', {
        'page': page, 'size': size,
        "appid": $("#app").val(),
        "app_item": $("#form_name").val(),
        "status": $("#status").val(),
    }, function (data) {

        getTable().update(
            {
                columns: [
                    {label: 'ID', field: 'id'},
                    {label: '操作', field: 'action'},
                    {label: '状态', field: 'state'},
                    {label: '商户', field: 'app_name'},
                    {label: '商品', field: 'app_item'},
                    {label: '实收', field: 'real_price'},
                    {label: '应收', field: 'price'},
                    {label: '订单ID', field: 'order_id'},
                    {label: '创建时间', field: 'create_time'},
                    {label: '支付时间', field: 'pay_time'},
                    {label: '关闭时间', field: 'close_time'},

                ],
                rows: data.data.map((row) => {
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
                    var action = ``;
                    if (row.state !== 3) {
                        action = `
 <button class="recallback-btn btn ms-2 btn-primary btn-floating btn-sm" data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fa fa-rotate"></i></button>`;
                    }
                    // jshint ignore:start
                    return Object.assign({}, row, {
                        state: state,
                        create_time: dateFormat("yyyy-MM-dd hh:mm:ss", row.create_time),
                        pay_time: dateFormat("yyyy-MM-dd hh:mm:ss", row.pay_time),
                        close_time: dateFormat("yyyy-MM-dd hh:mm:ss", row.close_time),
                        action: action,
                    });
                    // jshint ignore:end
                })

            },
            {loading: false}
        );
        new Pagination(document.querySelector('#pagination'), {
            current: page,
            total: data.count,
            size: size,
            onPageChanged: (page) => {
                loadTable(page, size);
            }
        }).render();

        $(".recallback-btn").off().on('click', function () {
            var json = JSON.parse(decodeURIComponent($(this).data("data")));
            $.post("/api/admin/order/callback", {order_id: json.order_id}, function (data) {

                if(data.code!==200){
                    $("#error_msg_body").text(data.msg);
                    mdb.Alert.getInstance(document.getElementById('error_msg')).show();
                }else{
                    loadTable(page, 10);
                    $("#success_msg_body").text(data.msg);
                    mdb.Alert.getInstance(document.getElementById('success_msg')).show();
                }
            });
        });
    }, "json");

}

getTable().update({}, {loading: true});
loadTable(1, 10);
$("#search").on("click", function () {
    loadTable(1, 10);
});



