// IIFE (Immediately Invoked Function Expression) to avoid polluting global namespace
(function (global, document, $) {
    "use strict";
    var tables = {
        module: {
            "[data-mdb-toggle=\"animation\"]": "mdb.Animate",
            "[data-mdb-spy=\"scroll\"]": "mdb.ScrollSpy",
            ".sidenav": "mdb.Sidenav",
            ".carousel": "mdb.Carousel",
            ".alert": "mdb.Alert",
            ".btn": "mdb.Button",
            ".chip": "mdb.ChipsInput",
            ".chips": "mdb.ChipsInput",
            ".collapse": "mdb.Collapse",
            ".dropdown-toggle": "mdb.Dropdown",
            ".lightbox": "mdb.Lightbox",
            "[role=\"tablist\"]": "mdb.Tab",
            ".list-group": "mdb.Tab",
            ".select": "mdb.Select",
            ".modal": "mdb.Modal",
            ".popconfirm-toggle": "mdb.Popconfirm",
            "[data-mdb-toggle=popover]": "mdb.Popover",
            ".rating": "mdb.Rating",
            ".stepper": "mdb.Stepper",
            ".toast": "mdb.Toast",
            "[data-mdb-toggle=\"tooltip\"]": "mdb.Tooltip",
            ".datepicker": "mdb.Datepicker",
            ".datetimepicker": "mdb.Datetimepicker",

            ".form-outline": "mdb.Input",
            ".range": "mdb.Range",
            ".timepicker": "mdb.TimePicker",
            "[data-mdb-chart]": "mdb.Charts",
            "[data-mdb-lazy-src]": "mdb.LazyLoad",
            ".infinite-scroll": "mdb.InfiniteScroll",
            ".multi-range-slider": "mdb.MultiRangeSlider",
            ".clipboard": "mdb.Clipboard",
            ".loading": "mdb.Loading",

            ".ripple": "mdb.Ripple",
            "[data-mdb-perfect-scrollbar]": "mdb.PerfectScrollbar",
            "[data-mdb-smooth-scroll]": "mdb.SmoothScroll",
            ".sticky": "mdb.Sticky",
            ".btn-tap": "mdb.Touch",
        },
        plugin: {
            "[data-mdb-file-upload]": ["file-upload", "FileUpload"],
            ".calendar": ["calendar", 'Calendar'],
            ".captcha": ["captcha", "Captcha"],
            ".color-picker": ["color-picker", 'ColorPicker'],
            "[data-mdb-sortable]": ["drag-and-drop", "DragAndDrop"],
            "[data-mdb-countdown]": ["countdown", 'Countdown'],
            ".ecommerce-gallery": ["ecommerce-gallery", 'EcommerceGallery'],
            ".dummy": ["dummy", 'Dummy'],
            "[data-mdb-auto-filter]": ["filters", 'Filters'],
            "[data-mdb-input-mask]": ["inputmask", 'Inputmask'],
            ".multi-carousel": ["multi-carousel", 'MultiCarousel'],
            ".parallax": ["parallax", 'Parallax'],
            ".scrollStatus": ["scroll-status", 'ScrollStatus'],
            ".treetable": ["treetable", 'Treetable'],
            ".treeview": ["treeview", "Treeview"],
            ".wysiwyg": ["wysiwyg", "WYSIWYG"],
            ".onboarding": ["onboarding", "Onboarding"],
        }
    };

    class Loading {

        constructor(parent, msg) {
            let that = this;
            that.derfer = $.Deferred();
            this.id = $.uuid();
            this.parent = parent || "#app";
            msg = msg || "";
            loadingIndex++;
            const loader = `
    <div class="loading loading-delay ankio-loading " style="z-index: ${loadingIndex + 2}"  data-mdb-loader="${this.id}" data-mdb-backdrop-id="backdrop-${this.id}" data-mdb-scroll="false">
      <div class="spinner-border loading-icon text-succes"></div>
        <span class="loading-text">${msg}</span>
      </div>
`;
            that.elem = $("<div>").append(loader);
            mdbAdmin.autoLoadComponents(that.elem, function () {
                $(that.parent).append(that.elem);
                mdbAdmin.initComponents($(that.parent).last(), function () {
                    setTimeout(function () {
                        $("#backdrop-" + that.id).css("z-index", loadingIndex + 1);
                        that.derfer.resolve();
                    }, 0);
                });
            });
        }

        hide() {
            let that = this;
            $.when(this.derfer).then(function () {
                $.wait("#" + that.id, function () {
                    $(this).parent().prev().fadeOut("fast", function () {
                        $(this).remove();
                    });
                    $(this).parent().fadeOut("fast", function () {
                        $(this).remove();
                    });
                    $("body").css("overflow", "auto");
                });
            });

        }

    }

    class DataBase {

        constructor(config) {
            var that = this;
            that.derfer = $.Deferred();
            that.config = $.extend({}, {
                elem: "#database",
                url: "",
                param: {},
                pageElm: "#pagination",
                rowClick: function () {

                },
                page: 1,
                size: 15,
                clickableRows: false,
                onsuccess: function () {

                },
                selectable: false,
                multi: false,
                onSelected: function () {

                },
                columns: [
                    {
                        label: "ID", field: "id", render: function () {
                        }
                    }
                ]
            }, config);
            resourceLoader.jsAll("js/Pagination.js").done(function () {
                that.db = mdb.Datatable.getOrCreateInstance(document.querySelector(config.elem));
                that.db.update({
                        multi: config["multi"],
                        selectable: config["selectable"],
                        columns: config["columns"],
                        clickableRows: config["clickableRows"],
                    },
                    {loading: false});

                that.reload(config.param);
                that.derfer.resolve();
            });
        }

        reload(param) {
            var that = this;
            $.when(this.derfer).then(function () {
                function syncData(page, size, param) {
                    $.ajax({
                        url: that.config["url"],  // 请求的URL
                        method: "GET",                  // 请求方法（GET、POST等）
                        data: $.extend({}, {page: page, size: size}, param),
                        dataType: "json",
                        success: function (response) {
                            if (response.code !== 200) {

                                mdbAdmin.toast.error(response.msg);
                                return;
                            }
                            var raw = JSON.parse(JSON.stringify(response.data));


                            var result = $.map(raw, function (row, index) {
                                var item = response.data[index];
                                $.each(that.config["columns"], function (k, v) {

                                    let field = v["field"];
                                    let func = v.hasOwnProperty("render") ? v.render : null;
                                    if (typeof func === "function") {
                                        let result = func(item, index, response);
                                        if (result === undefined || result === null) {
                                            row[field] = item.hasOwnProperty(field) ? item[field] : "";
                                        } else {
                                            row[field] = result;
                                        }

                                    }
                                });
                                return row;
                            });
                            that.db.update({rows: result});

                            $(that.config.elem)
                                .off("rowClick.mdb.datatable")
                                .on("rowClick.mdb.datatable", function (e) {

                                    const {index} = e;
                                    if (index === null) {
                                        return;
                                    }
                                    const func = that.config["rowClick"];
                                    if (typeof func === "function") {
                                        func(response.data[index], that.config, response, raw[index], index);
                                    }

                                })
                                .off("selectRows.mdb.datatable")
                                .on("selectRows.mdb.datatable", function (e) {

                                    const func = that.config["onSelected"];
                                    if (typeof func === "function") {
                                        var $array = [], $raw = [];
                                        $.each(e.selectedIndexes, function (k, v) {
                                            $array.push(response.data[v]);
                                            $raw.push(raw[v]);
                                        });
                                        func($array, e.selectedIndexes, that.config, response, $raw);
                                    }
                                });


                            resourceLoader.jsAll("js/Pagination.js").done(function () {
                                new Pagination(document.querySelector(that.config.pageElm), {
                                    current: that.config["page"],
                                    total: response.count,
                                    size: that.config["size"],
                                    onPageChanged: (page) => {
                                        that.config["page"] = page;
                                        syncData(page, that.config["size"], param);
                                    }
                                }).render();
                            });
                            const func = that.config["onsuccess"];
                            if (typeof func === "function") {
                                func(response.data, that.config, response, that.db);
                            }
                        },
                        error: function (xhr, status, error) {
                            log.danger(error);
                            mdbAdmin.toast.error("网络异常");
                        }
                    });
                }

                syncData(that.config["page"], that.config["size"], param || {});
            });
        }

        destroy() {
            var that = this;
            $.when(this.derfer).then(function () {
                that.db.dispose();
            });
        }

    }

    global.mdbAdmin = {


        initComponents(parent, onInitSuccess) {

            parent = parent || "#app";

            function getOptions($elem) {
                var dataObj = {};
                $.each($($elem).data(), function (key, value) {
                    if (key.startsWith("mdb")) {
                        // 在jQuery中，所有data属性已被转换为camelCase
                        var processedValue = value;
                        // 处理布尔值和数字，jQuery通常会自动处理这些
                        if (processedValue === "true") {
                            processedValue = true;
                        } else if (processedValue === "false") {
                            processedValue = false;
                        } else if (processedValue === "") {
                            processedValue = "";
                        } else if ($.isNumeric(processedValue)) {
                            processedValue = parseFloat(processedValue);
                        }

                        // 处理数组和对象
                        if (typeof processedValue === "string" && processedValue.startsWith("[")) {
                            processedValue = eval(processedValue);
                        } else if (typeof processedValue === "string" && processedValue.startsWith("{")) {
                            processedValue = JSON.parse(processedValue);
                        }

                        function lowercaseFirstLetter(string) {
                            return string.charAt(0).toLowerCase() + string.slice(1);
                        }

                        key = lowercaseFirstLetter(key.substring(3)).replace("Id", "ID");

                        dataObj[key] = processedValue;
                    }
                });
                return dataObj;
            }

            function each(type) {
                $.each(tables[type], function (k, raw) {
                    var elem;
                    if (typeof parent === "string") {
                        elem = $(parent + " " + k);
                    } else {
                        elem = parent.find(k);
                    }
                    if ($.isArray(raw)) {
                        raw = raw[1];
                    }
                    var func;
                    if (raw.startsWith("mdb.")) {
                        func = mdb[raw.substring(4)];
                    } else {
                        func = global[raw];
                    }
                    elem.each(function (kk, vv) {
                        function callback(func) {
                            try {
                                var option = getOptions(vv);
                                if (vv["function_" + raw]) {
                                    return;
                                }
                               window.debug && console.log("组件参数", raw, vv, option);
                                let objection = null;
                                if ($.isFunction(func.getOrCreateInstance)) {
                                    objection = func.getOrCreateInstance(vv, option);
                                } else if ($.isFunction(func.getInstance)) {
                                    objection = func.getInstance(vv);
                                    if (!objection) {
                                        objection = new func(vv, option);
                                        if ($.isFunction(objection.init)) {
                                            objection.init();
                                        }
                                    }
                                } else {
                                    objection = new func(vv, option);
                                    if ($.isFunction(objection.init)) {
                                        objection.init();
                                    }
                                }
                                vv["function_" + raw] = true;

                            } catch (e) {
                                log.danger(e, "组件初始化异常");
                                console.log(e);
                            }
                        }

                        if (func) {
                            callback(func);
                        } else {
                            log.primary(raw + "未曾加载");
                        }

                    });


                });

            }

            each("module");
            each("plugin");


            resetTheme();
            if ($.isFunction(onInitSuccess)) {
                onInitSuccess();
            }
        },
        modal: {
            position: {
                topRight: "modal-side  modal-top-right",
                topLeft: "modal-side modal-top-left",
                bottomRight: "modal-side  modal-bottom-right",
                bottomLeft: "modal-side  modal-bottom-right",
                center: "modal-dialog-centered modal-dialog-scrollable"
            },
            size: {
                small: "modal-sm",
                default: "",
                large: "modal-lg",
                extraLarge: "modal-xl",
                full: "modal-fullscreen"
            },
            color: {
                // primary:"bg-primary",
                primary: ["primary", "white"],
                success: ["success", "white"],
                error: ["danger", "white"],
                info: ["info", "white"],
                warning: ["warning", "white"]
            },
            show(config) {
                config = $.extend({}, {
                    title: "",
                    body: "",
                    position: this.position.center,
                    size: this.size.default,
                    color: this.color.primary,
                    buttons: [
                        [lang("关闭")], [lang("确定")]
                    ],
                    onclose: function () {

                    },
                    oncreate: function (dom) {

                    },
                    onrender: function (dom) {

                    }
                }, config);
                loadingIndex++;
                const id = "modal-" + loadingIndex.toString();
                var tpl = `
            <div class="modal fade" id="${id}" style="z-index:${loadingIndex + 2}" tabindex="-1"   data-mdb-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  ${config["position"]} ${config["size"]}">
    <div class="modal-content">
      <div class="modal-header bg-${config["color"][0]} text-${config["color"][1]}">
        <h5 class="modal-title " >${config["title"]}</h5>
        <button type="button" class="btn-close btn-close-${config["color"][1]}" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">${config["body"]}</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-${config["color"][0]}" >${config["buttons"][0][0]}</button>
        <button type="button" class="btn  btn-${config["color"][0]}" >${config["buttons"][1][0]}</button>
      </div>
    </div>
  </div>
</div>
            `;
                var dom = $(tpl);
                let func = config.oncreate;
                if (typeof func === "function") {
                    func(dom);
                }


                mdbAdmin.autoLoadComponents(dom, function () {
                    $("body").append(dom);

                    mdbAdmin.initComponents("#" + id);

                    let func = config.onrender;
                    if (typeof func === "function") {
                        func(dom, id);
                    }
                    mdb.Modal.getOrCreateInstance(document.getElementById(id)).show();
                    dom.next(".modal-backdrop").css("z-index", loadingIndex );
                    $("#" + id + " .modal-footer button:eq(0)").on("click", function () {
                        let func = config.buttons[0][1];
                        if (typeof func === "function") {
                            func(id);

                        }
                        mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
                    });
                    $("#" + id + " .modal-footer button:eq(1)").on("click", function () {
                        var form = $("#" + id + " form");
                        if (form.length > 0) {
                            form.trigger("submit");
                        } else {
                            let func = config.buttons[1][1];
                            if (typeof func === "function") {
                                func(id);
                            }
                            mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
                        }

                    });

                    $("#" + id).off().on("hidden.mdb.modal", function () {
                        let func = config.onclose;
                        if (typeof func === "function") {
                            func(id);
                        }
                        $(this).remove();
                    });
                });

            }
        },
        toast: {
            success(msg, title) {
                title = title || lang("成功");
                this.custom(msg, title, "success");
            },
            error(msg, title) {
                title = title || lang("错误");
                this.custom(msg, title, "danger");
            },
            info(msg, title) {
                title = title || lang("提示");
                this.custom(msg, title, "info");
            },
            warning(msg, title) {
                title = title || lang("警告");
                this.custom(msg, title, "warning");
            }, custom(msg, title, color) {
                loadingIndex++;
                const id = "toast-" + loadingIndex.toString();
                var tpl = `
    <div 
     class="toast fade mx-auto"
  role="alert"
  aria-live="assertive"
  aria-atomic="true"
  data-mdb-autohide="true"
  data-mdb-delay="4000"
  data-mdb-position="top-right"
  data-mdb-append-to-body="true"
  data-mdb-stacking="true"
  data-mdb-width="250px"
  data-mdb-color="${color}"
      id="${id}"
    >
      <div class="toast-header">
        <i class="fas fa-exclamation-circle fa-lg me-2"></i>
        <strong class="me-auto">${title}</strong>
        <small></small>
        <button type="button" class="btn-close" data-mdb-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">${msg}</div>
    
  </div>
            `;
                tpl = $("<div>").html(tpl);
                mdbAdmin.autoLoadComponents(tpl, function () {
                    $("body").append(tpl.html());
                    mdbAdmin.initComponents($("body").last());
                    mdb.Toast.getOrCreateInstance(document.getElementById(id)).show();
                    $("#" + id).css("z-index", loadingIndex + 3);
                    $("#" + id).off().on("hidden.mdb.toast", function () {
                        $(this).remove();
                    });
                });


            }
        },
        device() {
            const ua = navigator.userAgent.toLowerCase();
            const android = !!ua.match(/android|adr/i);
            const ios = !!ua.match(/(iphone|ipod|ipad);?/i);
            const wechat = ua.indexOf("micromessenger") !== -1;
            const kindle = ua.indexOf("kindle") !== -1;
            return {
                android: android,
                ios: ios,
                wechat: wechat,
                kindle: kindle,
                mobile: android || ios || wechat || kindle
            };
        },
        database(config) {

            return new DataBase(config);

        },
        upload(config) {
            config = $.extend({}, {
                elem: "",
                url: "",
                dom: "",
                msg: "",
                onsuccess: function () {

                }
            }, config);
            var dom = config.dom || "#app";
            var msg = config.msg;
            var loadings;

            let uploadInfo = false;
            $(config.elem).off().on("fileAdd.mdb.fileUpload", function (e) {
                if (uploadInfo) return;
                uploadInfo = true;

                const addedFile = e.files;
                const data = new FormData();
                data.append("file", addedFile[0]);
                var that = this;
                $.ajax({
                    type: "POST",
                    url: config.url,
                    data: data,
                    dataType: "json",
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        loadings = mdbAdmin.loading(dom, msg);
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        // 监听上传进度事件
                        xhr.upload.addEventListener("progress", function (event) {
                            if (event.lengthComputable) {
                                var percentComplete = event.loaded / event.total * 100;
                                $("#" + loadings.id).find(".loading-text").html(percentComplete.toFixed(2) + "%");
                            }
                        }, false);

                        return xhr;
                    },
                    error: function (error) {
                        log.danger("请求错误", error);
                        mdbAdmin.toast.error(lang("文件上传失败"), lang("网络错误"));

                    },
                    success: function (ret) {
                        if (ret.code !== 200) {
                            mdbAdmin.toast.error(ret.msg, lang("文件上传失败"));
                        } else {
                            mdbAdmin.toast.success(lang("文件上传成功！"));
                            sessionStorage.setItem("file_" + $(that).attr("name"), ret.data);
                            if (typeof config.onsuccess === "function") {
                                config.onsuccess(ret.data);
                            }
                        }
                    },
                    complete: function () {
                        loadings.hide();
                        uploadInfo = false;
                    }
                });
            });
        },

        dateFormat(fmt, date) {
            date = date === undefined ? new Date() : new Date(parseInt(date) * 1000);
            fmt = fmt || lang("yyyy年M月d日");
            var o = {
                "M+": date.getMonth() + 1,
                "d+": date.getDate(),
                "h+": date.getHours(),
                "m+": date.getMinutes(),
                "s+": date.getSeconds(),
                "q+": Math.floor((date.getMonth() + 3) / 3),
                "S": date.getMilliseconds()
            };
            if (/(y+)/.test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
            }

            for (const k in o) {
                if (new RegExp("(" + k + ")").test(fmt)) {
                    fmt = fmt.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
                }

            }
            return fmt;
        },
        alert: {
            success(msg) {
                this.custom(msg, "success");
            },
            error(msg) {
                this.custom(msg, "danger");
            },
            info(msg) {
                this.custom(msg, "info");
            },
            warning(msg) {
                this.custom(msg, "warning");
            },
            custom(msg, color) {

                const id = "alert-" + (++loadingIndex).toString();
                const tpl = `
            <div 
  class="alert fade"
  id="alert-primary"
  role="alert"
  data-mdb-color="${color}"
  data-mdb-position="top-right"
  data-mdb-stacking="true"
  data-mdb-width="535px"
  data-mdb-append-to-body="true"
  data-mdb-hidden="true"
  data-mdb-autohide="true"
  data-mdb-delay="4000"
>
 ${msg}
</div>
            `;
                $("body").append(tpl);
                resourceLoader.module("alert", function () {
                    mdb.Alert.getOrCreateInstance(document.getElementById(id)).show();
                    resetTheme();
                    $(id).css("z-index", loadingIndex + 3);
                });

                $("#" + id).on("closed.mdb.alert", function () {
                    $(this).remove();
                });

            }
        },
        loading(parent, msg) {
            return new Loading(parent, msg);
        },

        autoLoadComponents(dom, callback) {

            if (typeof dom === "string") {
                dom = $("<div>").append(dom);
            }

            var load = [];

            function loadComponents(type) {
                $.each(tables[type], function (key, value) {
                    if ($.isArray(value)) {
                        value = value[0];
                    }
                    if (dom.find(key).length > 0 && !value.startsWith("mdb")) {
                        let promise = $.Deferred();

                        if (type === "module") {
                            resourceLoader.module(value, function () {
                                promise.resolve();
                            });
                        } else {
                            resourceLoader.use(value, function () {
                                promise.resolve();
                            });
                        }
                        load.push(promise);
                    }
                });
            }

            loadComponents("module");
            loadComponents("plugin");

            $.when.apply($, load).then(function () {
                callback();
            });
        }
    };

    log.info("Welcome to use mdbAdmin.");


})(window, document, jQuery);

var loadingIndex = 19999999;




