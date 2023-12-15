route("shop/index", {
    depends:"index/shop/list",
    reference:"shop",
    container:"#container",
    title: "商品列表",
    onenter: function (query, dom,result) {
        let tpl = ``;

        $.each(result.data,function (key,item) {
            tpl+= `<div class="p-5 text-center ">
         <h1 class="mb-0 h3" >${key}</h1></div>
    <section class="text-center">
        <div class="row">`;

            $.each(item,function (index,items) {

                tpl +=` 
          <div class="col-xl-3 col-md-4 col-sm-6 mb-4 "><a href="/@shop/detail/${items['id']}"
                                                                 class="text-reset">
                        <div class="card">
                            <div class="bg-image hover-zoom ripple" data-mdb-ripple-color="light">
                            <img
                                        src="${items['image']}" class="w-100 " style="height: 150px"/>
                                <div class="mask">
                                    <div class="d-flex justify-content-start align-items-end h-100"><h5><span
                                                    class="badge badge-primary ms-2">${key}</span></h5></div>
                                </div>
                                <div class="hover-overlay">
                                    <div class="mask" style="background-color: rgba(251, 251, 251, 0.15)"></div>
                                </div>
                            </div>
                            <div class="card-body"><h4 class="card-title mb-3">${items['item']}</h4> <h6
                                        class="mb-3 text-danger">￥ <b
                                            class="h3">${items['price']}</b></h6></div>
                        </div>
                    </a></div>
   
        `;

            });

            tpl+= `</div>
    </section>`;
        });


        dom.find("#listContainer").html(tpl);


    },
    onrender: function (query, dom,result) {

    },
    onexit: function () {

    },
});
