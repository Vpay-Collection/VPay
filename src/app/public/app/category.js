/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

function getTable() {
    return mdb.Datatable.getOrCreateInstance(document.getElementById('datatable'));
}

function loadTable(page, size) {
    $.post('/api/admin/shop/category', {
        'page': page, 'size': size
    }, function (data) {
// jshint ignore:start
        getTable().update(
            {
                columns: [
                    {label: 'ID', field: 'id'},
                    {label: '分类', field: 'name'},
                    {label: '操作', field: 'action'}
                ],
                rows: data.data.map((row) => {
                    //  console.log(row);
                    var action = `
      <button class="edit-btn btn btn-outline-primary btn-floating btn-sm"  data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fas fa-pen"></i></button>
      <button class="delete-btn btn ms-2 btn-primary btn-floating btn-sm" data-data="${encodeURIComponent(JSON.stringify(row))}"><i class="fa fa-trash"></i></button>`;
                    return Object.assign({}, row, {
                        action: row.id !== 1 ? action : '',
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
            $("#addApp").click();
        });
        $(".delete-btn").off().on('click', function () {
            var json = JSON.parse(decodeURIComponent($(this).data("data")));
            $.post("/api/admin/shop/delCategory", {id: json.id}, function () {
                loadTable(page, size);
            });
        });
    }, "json");
}

getTable().update({}, {loading: true});
loadTable(1, 10);
$("#saveOrUpdate").off().on("click", function () {
    var data = form.val("form");
    $.post("/api/admin/shop/addOrUpdateCategory", data, function () {
        loadTable(1, 10);
    });
});
$('#addOrUpdate').off('hidden.bs.modal').on('hidden.bs.modal', function () {
    form.reset("form");
});



