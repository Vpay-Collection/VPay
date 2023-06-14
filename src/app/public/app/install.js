setTimeout(function () {
   new mdb.Stepper(document.getElementById('step'));
    //stepper.nextStep();
    //stepper.previousStep();
},500);

$("form").on('submit',function () {
   var data = form.val("form");
   $.post('/api/index/main/install',data,function (d) {
      if(d.code!==200){
         $("#errorInfo").html(d.msg);
          mdb.Modal.getOrCreateInstance(document.getElementById('error')).show();
      }else{
         $("#username_modal").html(data['_username']);
         $("#password_modal").html(data['_password']);
         mdb.Modal.getOrCreateInstance(document.getElementById('success')).show();

      }
   },'json');
   return false;
});