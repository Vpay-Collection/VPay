var form = {
    set(formElem, jsonData) {
        // 获取表单元素
        var formElements = $(formElem).find(":input:not(:reset):not(:button):not(:submit)[name]:not(:file)");
        // 参数设置
        form.reset(formElem);
        // 传入的json数据非空时
        if (jsonData && $.type(jsonData) === 'object' && !$.isEmptyObject(jsonData)) {


            // 遍历容器内所有表单元素
            formElements.each(function () {
                // 表单元素名称
                var name = $(this).attr("name");
                // jsonData key 值（移除 name 的前缀和后缀）
                var key = name;

                if (jsonData.hasOwnProperty(key)) {

                    // 当表单元素为 radio 时
                    if ($(this).is(":radio")) {
                        // 取消 radio 选中状态
                        formElements.filter(":radio[name='" + name + "']").prop("checked", false);
                        // 选中 radio
                        formElements.filter(":radio[name='" + name + "'][value=" + jsonData[key] + "]").prop("checked", true);
                    }
                    // 当表单元素为 checkbox 时
                    else if ($(this).is(":checkbox")) {
                        // 取消 checkbox 选中状态
                        formElements.filter(":checkbox[name='" + name + "']").prop("checked", false);
                        var checkBoxData = jsonData[key];
                        if ($.type(checkBoxData) === 'array') {
                            $.each(checkBoxData, function (i, val) {
                                formElements.filter(":checkbox[name='" + name + "'][value=" + val + "]").prop("checked", true);
                            });
                        } else if ($.type(checkBoxData) === 'string') {
                            $.each(checkBoxData.split(","), function (i, val) {
                                formElements.filter(":checkbox[name='" + name + "'][value=" + val + "]").prop("checked", true);
                            });
                        } else {
                            formElements.filter(":checkbox[name='" + name + "'][value=" + checkBoxData + "]").prop("checked", true);
                        }
                    } else {
                        $(this).val(jsonData[key]);
                    }
                }
            });
        }
        return $(formElem);
    },
    get(formElem) {
        var jsonData = {};
        // 获取表单元素
        var formElements = $(formElem).find(":input:not(:reset):not(:button):not(:submit)[name]");
        // 遍历所有表单元素
        formElements.each(function () {

            // 表单元素名称
            var name = $(this).attr("name");
            // key 值（移除 name 的前缀和后缀）
            var key = name;
            // 表单元素值
            var val = $(this).val();
            if ($(this).is(":file")) {
                val = sessionStorage.getItem(name);
            }
            // 当为 checkbox 时
            if ($(this).is(":checkbox")) {
                // checkbox 为多选时（有多个相同 name 的 checkbox）,将选中的值存入数组或拼接字符串
                if (formElements.filter(":checkbox[name='" + name + "']").length > 1) {
                    // 将选中的值拼接至字符串，以逗号分割
                    var str = jsonData.hasOwnProperty(key) ? jsonData[key] : "";
                    jsonData[key] = str;

                    if ($(this).is(":checked")) {
                        str += str.length > 0 ? ',' + val : val;
                        jsonData[key] = str;
                    }
                }
                // checkbox 为单选时
                else {
                    jsonData[key] = "";
                    if ($(this).is(":checked")) {
                        jsonData[key] = val;
                    }
                }
            }
            // 当为 radio 时
            else if ($(this).is(":radio")) {
                // 所有相同name的radio均为选中时，设置默认值""
                if (formElements.filter(":radio:checked[name='" + name + "']").length === 0) {
                    jsonData[key] = "";
                }
                if ($(this).is(":checked")) {
                    jsonData[key] = val;
                }
            }
            // 其他情况
            else {
                jsonData[key] = val;

            }
        });
        return jsonData;
    },
    val(formElem, value) {
        if (value !== undefined) {

            this.set(formElem, value);
        } else {
            return this.get(formElem);
        }
    },
    reset(formElem) {
        $(formElem).find(":input[type!='button'][type!='radio'][type!='checkbox'][type!='reset'][type!='submit']").val("");
        $(formElem).find(":checkbox,:radio").prop("checked", false);
        $(formElem).filter(":input[type!='button'][type!='file'][type!='image'][type!='radio'][type!='checkbox'][type!='reset'][type!='submit']").val("");
        $(formElem).filter(":checkbox,:radio").prop("checked", false);
        return $(formElem);
    },
    submit(formElem, fn) {
        $(formElem).submit(function () {
            if (typeof fn === "function") {
                fn(form.get(formElem));
            }
            return false;
        });
    }
};