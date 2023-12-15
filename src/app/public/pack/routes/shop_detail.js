route("shop/detail/:id", {
    depends:["index/shop/detail","index/shop/type"],
    reference:"shop",
    container:"#container",
    title: "商品信息",
    page: "shop/detail",
    onenter: function (query, dom,result) {

        replaceTpl(dom,result[0].data);
        dom.find("#description").html(result[0].data.description);

        $.each(result[1].data,function (index,value) {
          if(!value)  dom.find(`[data-type="${index}"]`).hide();
        });

        let inputs = result[0].data.inputs.split(",");
        let tpl = ``;
        function generateRandomString(length) {
            let randomString = '';
            let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            for (let i = 0; i < length; i++) {
                randomString += characters.charAt(Math.floor(Math.random() * characters.length));
            }

            return randomString;
        }
        $.each(inputs,function (k,v) {
            if(v.trim()==="")return;
            let str = generateRandomString(12);
            tpl+= ` <div class="form-outline mb-4"><input type="text" class="form-control" id="${str}" name="${v}"/>
                            <label class="form-label" for="${str}">${v}</label>
                        </div>`;
        });

        dom.find("#inputContainer").html(tpl);

    },
    onrender: function (query, dom,result) {
        dom.find("[data-type]").off().on("click",function () {
            let data = form.val("form");
            data['id'] = query.id;
            data['type'] = $(this).data("type");
            //发起请求购买商品
            request("index/shop/pay",data).done(function (data) {
                location.href = data.data;
            });
        });
    },
    onexit: function () {

    },
});
