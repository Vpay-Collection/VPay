$("form").off().on("submit", function () {
    var data = form.val("form");
    data.notice = $('#trumbowyg').trumbowyg('html');
    $.post("/api/admin/shop/config", data, function (data) {
        $("#success_msg_body").text(data.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
    });
    return false;
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