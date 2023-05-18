function getTable() {
    return mdb.Datatable.getOrCreateInstance(document.getElementById('datatable'));
}

function loadTable(page, size) {
    $.post('/api/admin/shop/items', {
        'page': page, 'size': size
        , 'item_category': $("#item_category").val()
        , 'name': $("#form_name").val()
    }, function (data) {
// jshint ignore:start
        getTable().update(
            {
                columns: [
                    {label: '封面', field: 'icon'},
                    {label: '名称', field: 'item_name'},
                    {label: '价格', field: 'item_price'},
                    {label: '操作', field: 'action'}
                ],
                rows: data.data.map((row) => {
                    //  console.log(row);
                    return Object.assign({}, row, {
                        icon: `<img
  src="${row.icon}"
  class="img-fluid"
  alt=""  height="fit-content" style="max-height: 100px"
/>`,
                        action: `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fas fa-pen"></i></button>
      <button class="delete-btn btn ms-2 btn-primary btn-floating btn-sm" data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fa fa-trash"></i></button>`,
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
            FileUpload.getInstance(elem).update({"defaultFile": json.icon});
            sessionStorage.setItem("icon", json.icon);

            $('#trumbowyg').trumbowyg('html', json["description_nofilter"]);
            $("#addApp").click();
        });
        $(".delete-btn").off().on('click', function () {
            var json = JSON.parse(decodeURIComponent($(this).data("data")));
            $.post("/api/admin/shop/delItem", {id: json.id}, function (ret) {
                if (ret.code !== 200) {
                    $("#error_msg_body").text(ret.msg);
                    mdb.Alert.getInstance(document.getElementById('error_msg')).show();
                } else {
                    $("#success_msg_body").text("删除成功");
                    mdb.Alert.getInstance(document.getElementById('success_msg')).show();
                }
                loadTable(page, size);
            });
        });
    }, "json");
}

getTable().update({}, {loading: true});
loadTable(1, 10);
$("#search").on("click", function () {
    loadTable(1, 10);
});
$("#file-upload").off().on('fileAdd.mdb.fileUpload', function (e) {
    const addedFile = e.files;
    const data = new FormData();
    data.append('file', addedFile[0]);
    $.ajax({
        type: 'POST',
        url: "/api/admin/shop/upload",        beforeSend() {
            loading.show();
        },
        complete(){
            loading.hide();
        },
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        success: function (ret) {
            if (ret.code !== 200) {
                $("#error_msg_body").text(ret.msg);
                mdb.Alert.getInstance(document.getElementById('error_msg')).show();
            } else {
                $("#success_msg_body").text("上传成功");
                mdb.Alert.getInstance(document.getElementById('success_msg')).show();
                sessionStorage.setItem("icon", ret.data);
            }
        }
    });
});
$("#saveOrUpdate").off().on("click", function () {
    var data = form.val("form");
    data["icon"] = sessionStorage.getItem("icon");
    data["description_nofilter"] = $('#trumbowyg').trumbowyg('html');

    $.post("/api/admin/shop/addOrUpdateItem", data, function (ret) {
        if (ret.code !== 200) {
            $("#error_msg_body").text(ret.msg);
            mdb.Alert.getInstance(document.getElementById('error_msg')).show();
        } else {
            $("#success_msg_body").text("添加/修改成功");
            mdb.Alert.getInstance(document.getElementById('success_msg')).show();
        }
        loadTable(1, 10);

    },"json");
});
$('#addOrUpdate').off().on('hidden.bs.modal', function () {
    form.reset("form");
    FileUpload.getInstance(document.querySelector("#file-upload")).update({"defaultFile": ""});
    sessionStorage.setItem("icon", "");
});

$('#trumbowyg').trumbowyg({
    lang: 'zh_cn'
    , btns: [
        //   ['undo', 'redo'], // Only supported in Blink browsers
        ['formatting'],
        ['strong', 'em', 'del'],
        ['foreColor', 'backColor'],
        ['superscript', 'subscript'],
        ['link'],
        ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen']
    ]
});

