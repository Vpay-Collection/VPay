function getTable() {
    return mdb.Datatable.getOrCreateInstance(document.getElementById('datatable'));
}

function loadTable(page, size) {
    $.post('/api/admin/app/list', {
        'page': page, 'size': size
    }, function (data) {
// jshint ignore:start
        getTable().update(
            {
                columns: [
                    {label: 'Logo', field: 'app_image'},
                    {label: '网站', field: 'app_name'},
                    {label: 'ID', field: 'id'},
                    {label: 'SecretKey', field: 'app_key'},
                    {label: '操作', field: 'action'}
                ],
                rows: data.data.map((row) => {
                    //  console.log(row);
                    var action = '';
                    if(row['id']!==1){
                        action = `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fas fa-pen"></i></button>
      <button class="delete-btn btn ms-2 btn-primary btn-floating btn-sm" data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fa fa-trash"></i></button>`
                    }
                    return Object.assign({}, row, {
                        app_image: `<img
  src="${row.app_image}"
  class="img-fluid rounded-circle"
  alt=""  height="fit-content" style="max-height: 50px"
/>`,
                        action: action,
                    });

                })

            },
            {loading: false}
        );
        // jshint ignore:end
        new Pagination(document.querySelector('#pagination'), {
            current: page,
            total: data.count,
            size: size,
            onPageChanged: (page) => {
                loadTable(page, size);
            }
        }).render();

        $(".edit-btn").off().on('click', function () {
            var json = JSON.parse(decodeURIComponent($(this).data("data")));
            form.val("#form", json);
            //   $(".file-upload-previews").html("<img src='"+json.icon+"'/>");
            var elem = document.querySelector("#file-upload");
            FileUpload.getInstance(elem).update({"defaultFile": json.app_image});
            sessionStorage.setItem("app_image", json.app_image);
            $("#addApp").click();
        });
        $(".delete-btn").off().on('click', function () {
            var json = JSON.parse(decodeURIComponent($(this).data("data")));
            $.post("/api/admin/app/del", {id: json.id}, function () {
                loadTable(page, size);
            });
        });
    }, "json");
}

getTable().update({}, {loading: true});
loadTable(1, 10);
$("#file-upload").off().on('fileAdd.mdb.fileUpload', function (e) {
    const addedFile = e.files;
    const data = new FormData();
    data.append('file', addedFile[0]);
    $.ajax({
        type: 'POST',
        url: "/api/admin/app/upload",
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend() {
            loading.show();
        },
        complete(){
            loading.hide();
        },
        success: function (ret) {
            if (ret.code !== 200) {
                $("#error_msg_body").text(data.msg);
                mdb.Alert.getInstance(document.getElementById('error_msg')).show();
            } else {
                sessionStorage.setItem("app_image", ret.data);
            }
        }
    });
});
$("#saveOrUpdate").off().on("click", function () {
    var data = form.val("form");
    data["app_image"] = sessionStorage.getItem("app_image");
    $.post("/api/admin/app/addOrUpdate", data, function () {
        loadTable(1, 10);
    });
});
$('#addOrUpdate').off().on('hidden.bs.modal', function () {
    form.reset("form");
    FileUpload.getInstance(document.querySelector("#file-upload")).update({"defaultFile": ""});
    sessionStorage.setItem("app_image", "");
});



