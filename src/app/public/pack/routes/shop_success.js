route("shop/success", {
    depends:'index/shop/return',
    reference:"shop",
    container:"#container",
    title: "支付成功",
    onenter: function (query, dom,result) {
        dom.find('#order-id').text(result.data.order_id);
        dom.find('#email').text(JSON.parse(result.data.param).mail); // 注意：这里可能需要解析param中的JSON字符串
        dom.find('#price').text(result.data.price);
        dom.find('#product').text(result.data.app_item);
    },
    onrender: function (query, dom,result) {

    },
    onexit: function () {

    },
});
