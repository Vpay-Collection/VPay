

const mdbAdmin = {
    initAdmin(menu, onerror,onsuccess) {

        request(menu, {},"请稍候")
          .done(function(data) {
              var html = "";
              var menu = data.data.menu;

              $.each(menu, function(k, v) {
                  if (v.child !== undefined) {
                      html += `
          <li class="sidenav-item">
          <a class="sidenav-link" href="javascript:void(0)"><i class="${v.icon} fa-fw me-3"></i>${v.name}</a>
          <ul class="sidenav-collapse">
          `;
                      $.each(v.child, function(kk, vv) {

                          html += `
            <li class="sidenav-item m-2 ">
              <a class="sidenav-link pt-3 pb-3" 
              href="${!vv.href.startsWith('http')?routeSegmentation+vv.href:vv.href}" 
                ${!vv.href.startsWith('http') ? "data-link=\"true\"" : "target=\"_blank\""}>
                <i class="${vv.icon} fa-fw me-3"></i>${vv.name}
                </a>
            </li>
          `;
                      });
                      html += `
        </ul>
        </li>`;
                  } else {
                      html += `
          <li class="sidenav-item">
          <a class="sidenav-link" 
          href="${!v.href.startsWith('http')?routeSegmentation+v.href:v.href}" 
             ${!v.href.startsWith('http') ? "data-link=\"true\"" : "target=\"_blank\""}>
            <i class="${v.icon} fa-fw me-3"></i>${v.name}
            </a>
          </li>`;
                  }

              });

              $(".sidenav-menu").html(html);
              $("#username").html(data.data.user.name);
              $("#image").attr("src", data.data.user.image);
              //重新初始化sidebar

              mdb.Sidenav.getOrCreateInstance(document.getElementById("sidenav-1"));
              if (typeof onsuccess == "function") {
                  onsuccess(data);
              }
              if(getHashFromURL(location.href)==="/"){
                  go(data.data.home.href);
              }
          }).fail(function(data) {
            if (typeof onerror == "function") {
                onerror(data);
            }
        });
    },

    initComponents(parent) {

        parent = parent || "#app";

        function getOptions(elem) {
            var dataObj = {};
            var attributes = elem.attributes;
            for (var i = 0; i < attributes.length; i++) {
                var attributeName = attributes[i].name;
                var attributeValue = attributes[i].value;

                if (attributeName.startsWith("data-mdb-")) {
                    // 检查属性名是否包含连字符
                    var key = attributeName.replace("data-mdb-", "");
                    if (key.includes("-")) {
                        key = key.replace(/-([a-zA-Z])/g, function(match, p1) {
                            return p1.toUpperCase();
                        });

                    }

                    if (attributeValue === "true") {
                        attributeValue = true;
                    } else if (attributeValue === "false") {
                        attributeValue = false;
                    } else if (attributeValue === "") {
                        attributeValue = "";
                    } else if (!isNaN(attributeValue)) {
                        attributeValue = parseFloat(attributeValue);
                    } else if (attributeValue.startsWith("[")) {
                        attributeValue = eval(attributeValue);
                    } else if (attributeValue.startsWith("{")) {
                        attributeValue = JSON.parse(attributeValue);
                    }
                    dataObj[key] = attributeValue;

                }
            }
            //  app.debug && log.info(dataObj, "元素属性");
            return dataObj;
        }

        function each(elem, func) {
            elem = parent + " " + elem;
            var el = document.querySelectorAll(elem);

            if (el !== null) {
                el.forEach(function(v) {
                    try{
                        func(v);
                    }catch (e) {
                        console.error("组件渲染错误",e)
                    }
                });
            }

        }


        each("[data-mdb-toggle=\"animation\"]", function(v) {

            new mdb.Animate(v, getOptions(v)).init();
        });
        each("[data-mdb-spy=\"scroll\"]", function(v) {
            new mdb.ScrollSpy(v, getOptions(v));
        });
        each(".sidenav", function(v) {
            new mdb.Sidenav(v, getOptions(v));
        });
        each(".carousel", function(v) {
            new mdb.Carousel(v, getOptions(v));
        });
        each(".alert", function(v) {
            new mdb.Alert(v, getOptions(v));
        });
        each(".btn", function(v) {
            new mdb.Button(v);
        });
        each(".carousel", function(v) {
            new mdb.Carousel(v, getOptions(v));
        });
        each(".chips", function(v) {
            new mdb.ChipsInput(v, getOptions(v));
        });
        each(".chip", function(v) {
            new mdb.ChipsInput(v, getOptions(v));
        });
        each(".collapse", function(v) {
            new mdb.Collapse(v, getOptions(v));
        });
        each(".dropdown-toggle", function(v) {
            new mdb.Dropdown(v, getOptions(v));
        });
        each(".lightbox", function(v) {
            new mdb.Lightbox(v, getOptions(v));
        });
        each("[role=\"tablist\"]", function(v) {
            new mdb.Tab(v);
        });
        each(".list-group", function(v) {
            new mdb.Tab(v);
        });
        each(".modal", function(v) {
            new mdb.Modal(v, getOptions(v));
        });
        each(".popconfirm-toggle", function(v) {
            new mdb.Popconfirm(v, getOptions(v));
        });
        each("[data-mdb-toggle=popover]", function(v) {
            new mdb.Popover(v, getOptions(v));
        });
        each(".rating", function(v) {
            new mdb.Rating(v, getOptions(v));
        });
        each(".stepper", function(v) {
            new mdb.Stepper(v, getOptions(v));
        });
        each(".toast", function(v) {
            new mdb.Toast(v, getOptions(v));
        });
        each("[data-mdb-toggle=\"tooltip\"]", function(v) {
            new mdb.Tooltip(v, getOptions(v));
        });
        each(".datepicker", function(v) {
            new mdb.Datepicker(v, getOptions(v));
        });
        each(".datetimepicker", function(v) {
            new mdb.Datetimepicker(v, getOptions(v));
        });
        each(".form-outline", function(v) {
            new mdb.Input(v).init();
        });
        each(".select", function(v) {
            mdb.Select.getOrCreateInstance(v, getOptions(v));
        });
        each(".range", function(v) {
            new mdb.Range(v, getOptions(v));
        });
        each(".timepicker", function(v) {
            new mdb.Timepicker(v, getOptions(v));
        });
        each("[data-mdb-chart]", function(v) {
            var obj = getOptions(v);
            var obj2 = {
                type: obj["chart"],
                data: {
                    labels: obj["labels"],
                    datasets: []
                }
            };
            var obj3 = {};
            $.each(obj, function(k, v) {
                if (k.startsWith("dataset")) {
                    k = k.replace(/dataset([a-zA-Z])/g, function(match, p1) {
                        return p1.toLowerCase();
                    });
                    obj3[k] = v;
                }
            });
            obj2["data"]["datasets"] = [obj3];
            new mdb.Chart(v, obj2);
        });
        each("[data-mdb-lazy-src]", function(v) {
            new mdb.LazyLoad(v, getOptions(v));
        });
        each(".infinite-scroll", function(v) {
            new mdb.InfiniteScroll(v, getOptions(v));
        });
        each(".multi-range-slider", function(v) {
            new mdb.MultiRangeSlider(v, getOptions(v));
        });
        each(".clipboard", function(v) {
            new mdb.Clipboard(v, getOptions(v));

        });
        each(".loading", function(v) {
            new mdb.Loading(v, getOptions(v));
        });
        each(".ripple", function(v) {
            new mdb.Ripple(v, getOptions(v));
        });
        each("[data-mdb-perfect-scrollbar]", function(v) {
            new mdb.PerfectScrollbar(v, getOptions(v));
        });
        each("[data-mdb-smooth-scroll]", function(v) {
            new mdb.SmoothScroll(v, getOptions(v));
        });
        each(".sticky", function(v) {
            new mdb.Sticky(v, getOptions(v));
        });
        each(".btn-tap", function(v) {
            new mdb.Touch(v, getOptions(v));
        });
        //拓展组件
        if (hasLoadPlugin("file-upload")) {
            each("[data-mdb-file-upload]", function(v) {
                var options = getOptions(v);
                if (FileUpload.getInstance(v, options) === null) {
                    new FileUpload(v, options);
                }

            });
        }

        if (hasLoadPlugin("calendar")) {
            each(".calendar", function(v) {
                new Calendar(v, getOptions(v));
            });
        }


        if (hasLoadPlugin("color-picker")) {
            each(".color-picker", function(v) {
                new ColorPicker(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("countdown")) {
            each("[data-mdb-countdown]", function(v) {
                new Countdown(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("calendar")) {
            each(".calendar", function(v) {
                new Calendar(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("dummy")) {
            each(".dummy", function(v) {
                new Dummy(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("ecommerce-gallery")) {
            each(".ecommerce-gallery", function(v) {
                new EcommerceGallery(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("filters")) {
            each("[data-mdb-auto-filter]", function(v) {
                new Filters(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("inputmask")) {
            each("[data-mdb-input-mask]", function(v) {
                new Inputmask(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("multi-carouse")) {
            each(".multi-carouse", function(v) {
                new MultiCarousel(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("parallax")) {
            each(".parallax", function(v) {
                new Parallax(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("scroll-status")) {
            each(".scrollStatus", function(v) {
                new ScrollStatus(v, getOptions(v));
            });
        }

        if (hasLoadPlugin("treetable")) {
            each(".treetable", function(v) {
                new Treetable(v);
            });
        }

        if (hasLoadPlugin("wysiwyg")) {
            each(".wysiwyg", function(v) {
                new WYSIWYG(v, $.extend({}, getOptions(v), {
                    wysiwygTranslations: {
                        paragraph: "段落",
                        textStyle: "文本样式",
                        heading: "标题",
                        preformatted: "预设格式",
                        bold: "加粗",
                        italic: "斜体",
                        strikethrough: "删除线",
                        underline: "下划线",
                        textcolor: "文本颜色",
                        textBackgroundColor: "文本背景颜色",
                        alignLeft: "左对齐",
                        alignCenter: "居中对齐",
                        alignRight: "右对齐",
                        alignJustify: "两端对齐",
                        insertLink: "插入链接",
                        insertPicture: "插入图片",
                        unorderedList: "无序列表",
                        orderedList: "有序列表",
                        increaseIndent: "增加缩进",
                        decreaseIndent: "减少缩进",
                        insertHorizontalRule: "插入水平线",
                        showHTML: "显示HTML代码",
                        undo: "撤销",
                        redo: "重做",
                        addLinkHead: "添加链接",
                        addImageHead: "添加图片",
                        linkUrlLabel: "输入网址：",
                        linkDescription: "输入描述",
                        imageUrlLabel: "输入图片网址：",
                        okButton: "确定",
                        cancelButton: "取消",
                        moreOptions: "更多选项"
                    }
                }));
            });
        }

        resetTheme();

    },
    /* jshint ignore:end */




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

        config = $.extend({}, {
            elem: "#database",
            url: "",
            param: {},
            pageElm: "#pagination",
            rowClick: function() {

            },
            page: 1,
            size: 15,
            clickableRows:false,
            onsuccess: function() {

            },
            selectable: false,
            multi: false,
            onSelected: function() {

            },
            columns: [
                {
                    label: "ID", field: "id", render: function() {
                    }
                }
            ]
        }, config);
        $.ajax({
            url: config["url"],  // 请求的URL
            method: "GET",                  // 请求方法（GET、POST等）
            data: $.extend({}, { page: config["page"], size: config["size"] }, config["param"]),
            dataType: "json",
            success: function(response) {
                if (response.code !== 200) {
                    if (response.code === 301 && response.data !== null) {
                        $.pjax({ url: response.data, container: "#app" });
                        return;

                    } else if (response.code === 302 && response.data !== null) {
                        location.href = response.data;
                        return;
                    }

                    mdbAdmin.toast.error(response.msg);
                    return;
                }
                var raw = JSON.parse(JSON.stringify(response.data));

                mdb.Datatable.getOrCreateInstance(document.querySelector(config.elem)).update({
                      multi: config["multi"],
                      selectable: config["selectable"],
                      columns: config["columns"],
                        clickableRows:config['clickableRows'],
                      rows: $.map(raw, function(row, index) {
                          var item = response.data[index];
                          $.each(config["columns"], function(k, v) {

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
                      })
                  },
                  { loading: false });


                $(config.elem)
                 .off("rowClick.mdb.datatable")
                    .on("rowClick.mdb.datatable", function(e) {

                      const { index } = e;
                      if (index === null) {
                          return;
                      }
                      const func = config["rowClick"];
                      if (typeof func === "function") {
                          func(response.data[index], config, response, raw[index], index);
                      }

                  })
                 .off("selectRows.mdb.datatable")
                    .on("selectRows.mdb.datatable", function(e) {

                      const func = config["onSelected"];
                      if (typeof func === "function") {
                          var $array = [], $raw = [];
                          $.each(e.selectedIndexes, function(k, v) {
                              $array.push(response.data[v]);
                              $raw.push(raw[v]);
                          });
                          func($array, e.selectedIndexes, config, response, $raw);
                      }
                  });


                new Pagination(document.querySelector(config.pageElm), {
                    current: config["page"],
                    total: response.count,
                    size: config["size"],
                    onPageChanged: (page) => {
                        config["page"] = page;
                        mdbAdmin.database(config);
                    }
                }).render();
                const func = config["onsuccess"];
                if (typeof func === "function") {
                    func(response.data, config, response);
                }
            },
            error: function(xhr, status, error) {
                log.error(error);
                mdbAdmin.toast.error("网络异常");
            }
        });


    },
    upload(config) {
        config = $.extend({}, {
            elem: "",
            url: "",
            dom: "",
            msg: "",
            onsuccess: function() {

            }
        }, config);
        var dom = config.dom || "#app";
        var msg = config.msg;
        var loadings = "";

        $(config.elem).off().on("fileAdd.mdb.fileUpload", function(e) {
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
                beforeSend: function() {
                    loadings = mdbAdmin.loading.show(dom, msg);
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    // 监听上传进度事件
                    xhr.upload.addEventListener("progress", function(event) {
                        if (event.lengthComputable) {
                            var percentComplete = event.loaded / event.total * 100;
                            $("#" + loadings).find(".loading-text").html(percentComplete.toFixed(2) + "%");
                        }
                    }, false);

                    return xhr;
                },
                error: function(error) {
                    log.error("请求错误", error);
                    mdbAdmin.toast.error("文件上传失败", "网络错误");

                },
                success: function(ret) {
                    if (ret.code !== 200) {
                        mdbAdmin.toast.error(ret.msg, "文件上传失败");
                    } else {
                        mdbAdmin.toast.success("文件上传成功！");
                        sessionStorage.setItem("file_" + $(that).attr("name"), ret.data);
                        if (typeof config.onsuccess === "function") {
                            config.onsuccess(ret.data);
                        }
                    }
                },
                complete: function() {
                    mdbAdmin.loading.hide(loadings);
                }
            });
        });
    },

    dateFormat(fmt, date) {
        date = date === undefined ? new Date() : new Date(parseInt(date) * 1000);
        fmt = fmt || "yyyy年M月d日";
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


};





