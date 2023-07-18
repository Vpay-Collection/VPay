/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

$(".submit-btn").on("click", function () {
    var data =  form.val("#buy");
    data = $.extend({},data,{
        'id':$(this).data("id"),'pay_type':$(this).data("type")
    });
   $.post("/api/shop/main/create",data,function (d) {
       if(d.code===200){
           location.href = d.data;
       }else{
           $("#error_msg_body").text(d.msg);
           mdb.Alert.getInstance(document.getElementById('error_msg')).show();
       }

   },"json");
});