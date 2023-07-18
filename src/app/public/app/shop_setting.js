
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

// jshint ignore:start

var wysiwygInstance = new WYSIWYG(document.getElementsByClassName('wysiwyg')[0], {
    wysiwygTranslations: {
        paragraph: '段落',
        textStyle: '文本样式',
        heading: '标题',
        preformatted: '预设格式',
        bold: '加粗',
        italic: '斜体',
        strikethrough: '删除线',
        underline: '下划线',
        textcolor: '文本颜色',
        textBackgroundColor: '文本背景颜色',
        alignLeft: '左对齐',
        alignCenter: '居中对齐',
        alignRight: '右对齐',
        alignJustify: '两端对齐',
        insertLink: '插入链接',
        insertPicture: '插入图片',
        unorderedList: '无序列表',
        orderedList: '有序列表',
        increaseIndent: '增加缩进',
        decreaseIndent: '减少缩进',
        insertHorizontalRule: '插入水平线',
        showHTML: '显示HTML代码',
        undo: '撤销',
        redo: '重做',
        addLinkHead: '添加链接',
        addImageHead: '添加图片',
        linkUrlLabel: '输入网址：',
        linkDescription: '输入描述',
        imageUrlLabel: '输入图片网址：',
        okButton: '确定',
        cancelButton: '取消',
        moreOptions: '更多选项',
    },
    wysiwygShowCodeSection:false,
    wysiwygUndoRedoSection:false,
    wysiwygLinksSection:false
});
//$(".wysiwyg-toolbar-group").hide();

$("form").off().on("submit", function () {
    var data = form.val("form");
    data.notice = wysiwygInstance.getCode();
    $.post("/api/admin/shop/config", data, function (data) {
        $("#success_msg_body").text(data.msg);
        mdb.Alert.getInstance(document.getElementById('success_msg')).show();
    });
    return false;
});
// jshint ignore:end