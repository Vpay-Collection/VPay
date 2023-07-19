/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

const app = {
    dir: "", // 缓存的路径文件
    libs: [],
    css: [],
    menuIndex: 0,
    base: "",
    loading: 0,
    initFunctions :null,
    pjax: false,
    debug: true,
    initComponents:[]
};
class Pagination {
    constructor(element, options = {}) {
        this.element = element;
        this.options = Object.assign({}, {
            current: 1,
            total: 1,
            size: 15,
            onPageChanged: () => {
            }
        }, options);

        this.previousText = element.dataset.previous;
        this.nextText = element.dataset.next;
    }

    render() {
        const {current, total, size} = this.options;
        sessionStorage.setItem("page",current);
        const pages = [];
        const totalPages = Math.ceil(total / size);
        const start = Math.max(1, current - 3);
        const end = Math.min(totalPages, current + 3);
        if (current > 1) {
            pages.push(this.createLink(current - 1, this.previousText, 'page-link me-1', 'aria-label', this.previousText));
        } else {
            pages.push(this.createLink(null, this.previousText, 'page-link disabled me-1', 'aria-label', this.previousText));
        }

        if (start > 1) {
            pages.push(this.createLink(1, '1', 'page-link', 'aria-current', 1 === current ? 'page' : null));
            if (start > 2) {
                pages.push(this.createLink(current - 5, '...', 'page-link', 'aria-label', 'More Pages'));
            }
        }

        for (let i = start; i <= end; i++) {
            pages.push(this.createLink(i, i, 'page-link', 'aria-current', i === current ? 'page' : null));
        }

        if (end < totalPages) {
            if (end < totalPages - 1) {
                pages.push(this.createLink(current + 5, '...', 'page-link', 'aria-label', 'More Pages'));
            }
            pages.push(this.createLink(totalPages, totalPages, 'page-link', 'aria-current', totalPages === current ? 'page' : null));
        }

        if (current < totalPages) {
            pages.push(this.createLink(current + 1, this.nextText, 'page-link ms-1', 'aria-label', this.nextText));
        } else {
            pages.push(this.createLink(null, this.nextText, 'page-link disabled ms-1', 'aria-label', this.nextText));
        }

        this.element.innerHTML = `
    <nav aria-label="Page navigation">
      <ul class="pagination  justify-content-center">
        ${pages.join('')}
      </ul>
    </nav>
  `;

        const links = this.element.querySelectorAll('.page-link:not(.disabled)');
        links.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const target = event.currentTarget;
                const page = target.getAttribute('data-page');
                this.options.current = parseInt(page, 10);
                this.render();
                this.options.onPageChanged(this.options.current);
            });
        });
    }


    /*jshint ignore:start*/
    createLink(page, text, classes = 'page-link', attribute = null, attributeValue = null) {
        var add = "";
        if (attribute && attributeValue) {
            add = attribute + '="' + attributeValue + '"'

        }
        if (this.options.current === page) {
            classes += ' active';
        }

        return `<li class="page-item"><a href="javascript:void(0)" class="${classes}" data-page="${page}" ${add}>${text}</a></li>`;
    }

    /*jshint ignore:end*/
    update(options) {
        this.options = Object.assign({}, this.options, options);
        this.render();
    }
}
const setMode = () => {
    let sidenavInstance = mdb.Sidenav.getOrCreateInstance(document.getElementById("sidenav-1"));
    // Check necessary for Android devices
    if (window.innerWidth < 1400) {
        sidenavInstance.changeMode("over");
        sidenavInstance.hide();
    } else {
        sidenavInstance.changeMode("side");
        sidenavInstance.show();
    }
};



function getPath() {
    if (app.dir !== "") {
        return app.dir;
    }
    const jsPath = window.document.currentScript
        ? window.document.currentScript.src
        : (function () {
            const js = window.document.scripts;
            const last = js.length - 1;
            let src;
            for (let i = last; i > 0; i--) {
                if (js[i].readyState === "interactive") {
                    src = js[i].src;
                    break;
                }
            }
            return src || js[last].src;
        })();
    return app.dir = jsPath.substring(0, jsPath.lastIndexOf("/") + 1);
}

getPath();
// Event listeners
window.addEventListener("resize", setMode);
/* jshint ignore:start */
const mdbAdminPlugins = {
    "all": "all",
    "calendar": "calendar",
    "color-picker": "color-picker",
    "cookie": "cookie",
    "countdown": "countdown",
    "data-parser": "data-parser",
    "drag-and-drop": "drag-and-drop",
    "dummy": "dummy",
    "ecommerce-gallery": "ecommerce-gallery",
    "file-upload": "file-upload",
    "filters": "filters",
    "inputmask": "inputmask",
    "mention": "mention",
    "multi-carousel": "multi-carousel",
    "onboarding": "onboarding",
    "organization-chart": "organization-chart",
    "parallax": "parallax",
    "scroll-status": "scroll-status",
    "storage": "storage",
    "table-editor": "table-editor",
    "transfer": "transfer",
    "treetable": "treetable",
    "treeview": "treeview",
    "vector-maps": "vector-maps",
    "wysiwyg": "wysiwyg"
};
function hasLoadPlugin(plugin) {
    return app.libs.includes(getPath() + "../mdb/plugins/js/" + plugin + ".min.js");
}

/* jshint ignore:end */


const mdbAdmin = {
    async requestResource(file, async) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", file, async);
        xhr.onload = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error(`请求加载失败: ${file}`));
                }
            }
        };
        xhr.onerror = function () {
            reject(new Error(`请求加载失败: ${file}`));
        };
        xhr.send(null);
    });
},
    async  loadCss(file, async) {
        if (app.css.includes(file)) {
            return;
        }// 如果已经在内存加载就直接返回
        try {
            const data = await this.requestResource(file, async);
            if (app.css.includes(file)) {
                return;
            }
            const style = window.document.createElement("style");
            style.innerHTML = data;
            window.document.head.appendChild(style);
            app.css.push(file);
        } catch (error) {
            console.error(error.message);
        }
    },

    async  loadJs(file, async, force) {
        if (!force && app.libs.includes(file)) {
            return;
        } // 如果已经在内存加载就直接返回
        try {
            const data = await this.requestResource(file, async);
            if (app.libs.includes(file)) {
                return;
            }
            const script = window.document.createElement("script");
            script.type = "text/javascript";
            script.text = data;
            window.document.head.appendChild(script);
            app.libs.push(file);
        } catch (error) {
            console.error(error.message);
        }
    },
    async useJs(models, async) {
        if (typeof models === "string") {
            models = [models];
        }
        async = !!async;
        for (const model of models) {
            await this.loadJs(getPath() + "../app/" + model + ".js", async);
        }

    },
    async use(models, async) {
        if (typeof models === "string") {
            models = [models];
        }
        async = !!async;
        for (const model of models) {
            await this.loadJs(getPath() + "../mdb/plugins/js/" + model + ".min.js", async);
            await this.loadCss(getPath() + "../mdb/plugins/css/" + model + ".min.css", async);
        }

    },
    initRoute() {
        function pathHtml(data) {
            var scriptsEval = "";
            var scriptHTML = "";
            $(data).filter('script').each(function (k,v) {
                var type = $(v).data("type");
                if(type!==undefined&&type.index("html")>0 || $(v).data('src')!==undefined){
                    scriptHTML+=v.outerHTML;
                }else{
                    scriptsEval+=v.innerHTML;
                }
            });
            data = $.map($(data).not('script'),function (elem) {
                return elem.outerHTML;
            }).join('');

            let container = $('#app');
            container.html(data);

            if(scriptsEval!=="") {
                // log.error(scriptsEval,"执行代码");
                try {
                    scriptsEval = "(function(){" + scriptsEval + "})();";
                    eval(scriptsEval);
                } catch (e) {
                    log.error(e);
                }
            }

            container.append(scriptHTML);

        }

        $(document).on('click', '[data-link]', function(event) {
            var currentURL = window.location.href;
            var targetURL = $(this).attr('href');

            if (currentURL === targetURL) {
                event.preventDefault();
                return;
            }

            // 使用PJAX加载目标URL的内容
            $.pjax({
                url: targetURL,
                container: '#app' // 指定要更新的容器
            });
            event.preventDefault();
        });


      //  $(document).pjax('[data-link]', '#app');
        $(document).on('pjax:send', function () {
            mdbAdmin.loading.show('#app');
        });
        $(document).on('pjax:beforeSend', function (event, xhr) {
            window.url = location.pathname;
            xhr.setRequestHeader('Authorization', localStorage.getItem('Authorization') || '');
        });
        $(document).on('pjax:beforeReplace', function (event, contents) {
            pathHtml(contents);
            return false;
        });

        $(document).on('pjax:complete', function () {
            mdbAdmin.initComponents();
            mdbAdmin.loading.hide();
            resetTheme();
        });
        $(document).on('pjax:timeout', function (event) {
            mdbAdmin.toast.error("网络错误");
            event.preventDefault();
        });
    },
    initAdmin(url, menu) {
        setMode();
        app.base = url;
        this.request(menu, {}, "get", {"#app": "页面加载中..."})
            .done(function (data) {
                var html = "";
                var menu = data.data.menu;

                $.each(menu, function (k, v) {
                    if (v.child !== undefined) {
                        html += `
          <li class="sidenav-item">
          <a class="sidenav-link" href="javascript:void(0)"><i class="${v.icon} fa-fw me-3"></i>${v.name}</a>
          <ul class="sidenav-collapse">
          `;
                        $.each(v.child, function (kk, vv) {

                            html += `
            <li class="sidenav-item">
              <a class="sidenav-link" 
              href="${vv.href}" 
                ${vv.inner ? 'data-link="true"' : ''} 
                ${!v.inner ? 'target="_blank"' : ''}>
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
          href="${v.href}" 
            ${v.inner ? 'data-link="true"' : ''}  
            ${!v.inner ? 'target="_blank"' : ''}>
            <i class="${v.icon} fa-fw me-3"></i>${v.name}
            </a>
          </li>`;
                    }

                });

                $(".sidenav-menu").html(html);
                $("#username").html(data.data.user.name);
                $("#image").attr("src", data.data.user.image);
                //重新初始化sidebar
                let sidenavInstance = mdb.Sidenav.getInstance(document.getElementById("sidenav-1"));
                sidenavInstance.dispose();
                mdb.Sidenav.getOrCreateInstance(document.getElementById("sidenav-1"));
                setMode();
                mdbAdmin.initRoute();
            });
    },
    /* jshint ignore:start */
    initComponents(parent,func) {
        if(typeof func === "function"){
            app.initFunctions = func;
        }

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
                        key = key.replace(/-([a-zA-Z])/g, function (match, p1) {
                            return p1.toUpperCase();
                        });

                    }

                    if (attributeValue === "true") {
                        attributeValue = true;
                    } else if (attributeValue === "false") {
                        attributeValue = false;
                    }else if (attributeValue === "") {
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
                el.forEach(function (v) {
                    //    app.debug && log.info(v, "处理元素");
                    func(v);
                    //    app.debug && log.success(v, "元素处理成功");
                });
            }

        }


        each('[data-mdb-toggle="animation"]', function (v) {
            new mdb.Animate(v, getOptions(v)).init();
        });
        each('[data-mdb-spy="scroll"]', function (v) {
            new mdb.ScrollSpy(v, getOptions(v));
        });
        each('.sidenav', function (v) {
            new mdb.Sidenav(v, getOptions(v));
        });
        each('.carousel', function (v) {
            new mdb.Carousel(v, getOptions(v));
        });
        each('.alert', function (v) {
            new mdb.Alert(v, getOptions(v));
        });
        each('.btn', function (v) {
            new mdb.Button(v);
        });
        each('.carousel', function (v) {
            new mdb.Carousel(v, getOptions(v));
        });
        each('.chips', function (v) {
            new mdb.ChipsInput(v, getOptions(v));
        });
        each('.chip', function (v) {
            new mdb.ChipsInput(v, getOptions(v));
        });
        each('.collapse', function (v) {
            new mdb.Collapse(v, getOptions(v));
        });
        each('.dropdown-toggle', function (v) {
            new mdb.Dropdown(v, getOptions(v));
        });
        each('.lightbox', function (v) {
            new mdb.Lightbox(v, getOptions(v));
        });
        each('[role="tablist"]', function (v) {
            new mdb.Tab(v);
        });
        each('.list-group', function (v) {
            new mdb.Tab(v);
        });
        each('.modal', function (v) {
            new mdb.Modal(v, getOptions(v));
        });
        each('.popconfirm-toggle', function (v) {
            new mdb.Popconfirm(v, getOptions(v));
        });
        each('[data-mdb-toggle=popover]', function (v) {
            new mdb.Popover(v, getOptions(v));
        });
        each('.rating', function (v) {
            new mdb.Rating(v, getOptions(v));
        });
        each('.stepper', function (v) {
            new mdb.Stepper(v, getOptions(v));
        });
        each('.toast', function (v) {
            new mdb.Toast(v, getOptions(v));
        });
        each('[data-mdb-toggle="tooltip"]', function (v) {
            new mdb.Tooltip(v, getOptions(v));
        });
        each('.datepicker', function (v) {
            new mdb.Datepicker(v, getOptions(v));
        });
        each('.datetimepicker', function (v) {
            new mdb.Datetimepicker(v, getOptions(v));
        });
        each('.form-outline', function (v) {
            new mdb.Input(v).init();
        });
        each('.select', function (v) {
            new mdb.Select(v, getOptions(v));
        });
        each('.range', function (v) {
            new mdb.Range(v, getOptions(v));
        });
        each('.timepicker', function (v) {
            new mdb.Timepicker(v, getOptions(v));
        });
        each('[data-mdb-chart]', function (v) {
            var obj = getOptions(v);
            var obj2 = {
                type: obj['chart'],
                data: {
                    labels: obj['labels'],
                    datasets: [],
                },
            };
            var obj3 = {};
            $.each(obj, function (k, v) {
                if (k.startsWith("dataset")) {
                    k = k.replace(/dataset([a-zA-Z])/g, function (match, p1) {
                        return p1.toLowerCase();
                    });
                    obj3[k] = v;
                }
            });
            obj2['data']['datasets'] = [obj3];
            new mdb.Chart(v, obj2);
        });
        each('[data-mdb-lazy-src]', function (v) {
            new mdb.LazyLoad(v, getOptions(v));
        });
        each('.infinite-scroll', function (v) {
            new mdb.InfiniteScroll(v, getOptions(v));
        });
        each('.multi-range-slider', function (v) {
            new mdb.MultiRangeSlider(v, getOptions(v));
        });
        each('.clipboard', function (v) {
            new mdb.Clipboard(v, getOptions(v));

        });
        each('.loading', function (v) {
            new mdb.Loading(v, getOptions(v));
        });
        each('.ripple', function (v) {
            new mdb.Ripple(v, getOptions(v));
        });
        each('[data-mdb-perfect-scrollbar]', function (v) {
            new mdb.PerfectScrollbar(v, getOptions(v));
        });
        each('[data-mdb-smooth-scroll]', function (v) {
            new mdb.SmoothScroll(v, getOptions(v));
        });
        each('.sticky', function (v) {
            new mdb.Sticky(v, getOptions(v));
        });
        each('.btn-tap', function (v) {
            new mdb.Touch(v, getOptions(v));
        });
        //拓展组件
        if(hasLoadPlugin("file-upload")){
            each('[data-mdb-file-upload]',function (v) {
                var options = getOptions(v);
                if(FileUpload.getInstance(v,options)===null){
                    new FileUpload(v,options);
                }

            });
        }

        if(hasLoadPlugin("calendar")){
            each('.calendar',function (v) {
                new Calendar(v,getOptions(v));
            });
        }


        if(hasLoadPlugin("color-picker")){
            each('.color-picker',function (v) {
                new ColorPicker(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("countdown")){
            each('[data-mdb-countdown]',function (v) {
                new Countdown(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("calendar")){
            each('.calendar',function (v) {
                new Calendar(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("dummy")){
            each('.dummy',function (v) {
                new Dummy(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("ecommerce-gallery")){
            each('.ecommerce-gallery',function (v) {
                new EcommerceGallery(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("filters")){
            each('[data-mdb-auto-filter]',function (v) {
                new Filters(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("inputmask")){
            each('[data-mdb-input-mask]',function (v) {
                new Inputmask(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("multi-carouse")){
            each('.multi-carouse',function (v) {
                new MultiCarousel(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("parallax")){
            each('.parallax',function (v) {
                new Parallax(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("scroll-status")){
            each('.scrollStatus',function (v) {
                new ScrollStatus(v,getOptions(v));
            });
        }

        if(hasLoadPlugin("treetable")){
            each('.treetable',function (v) {
                new Treetable(v);
            });
        }

        if(hasLoadPlugin("wysiwyg")){
            each('.wysiwyg',function (v) {
                new WYSIWYG(v,getOptions(v));
            });
        }

        resetTheme();

        if(typeof app.initFunctions === "function"){
            app.initFunctions(each,getOptions,parent);
        }
    },
    /* jshint ignore:end */
    loading: {
        show(parent, msg) {
            parent = parent || "#app";
            msg = msg || "";
            app.loading = app.loading + 1;
            var id = "loading-" + (app.loading + 1).toString();
            const loader = `
    <div class="loading-delay ankio-loading" id="${id}" >
      <div class="spinner-border loading-icon text-succes"></div>
        <span class="loading-text">${msg}</span>
      </div>
`;

            $(parent).append(loader);
            resetTheme();
            /* jshint -W031 */
            new mdb.Loading(document.querySelector("#" + id), {
                backdropID: 'backdrop-' + id,
            });
            /* jshint +W031 */
            return id;
        },
        hide(id) {
            id = id || "loading-" + app.loading.toString();
            if (document.querySelector("#" + id) == null) {
                let count = 1000;
                const intval = setInterval(function () {
                    if (count <= 0) {
                        clearInterval(intval);
                    }
                    count = count - 1;
                    let elem = $(".ankio-loading");
                    if (elem.length > 0) {
                        elem.remove();
                        $(".loading-backdrop").remove();
                        clearInterval(intval);
                    }

                }, 200);
            } else {
                document.querySelector("#" + id).style.opacity = "0";
                setTimeout(function () {
                    $("#" + id).remove();
                },500);
                $('#backdrop-' + id).remove();
            }

        }
    },
    request(url, data, method, dom) {
        method = method || "POST";
        data = data || {};
        dom = dom || {"#app": "正在请求中..."};
        const deferred = $.Deferred();
        var loadings = [];
        var u = app.base + url;
        u = u.replace("//","/");
        $.ajax({
            url: u,
            headers: {
                Authorization: localStorage.getItem('Authorization') || ''
            },
            type: method,
            data: data,
            dataType: "json",
            beforeSend: function () {
                if (dom !== {}) {
                    $.each(dom, function (k, v) {
                        loadings.push(mdbAdmin.loading.show(k, v));
                    });
                }
                if (app.debug) {
                    log.info(url, "请求URL");
                    log.info(data, "请求数据");
                }
            },
            success: function (data) {
                if (app.debug) {
                    log.success(url, "返回URL");
                    log.success(data, "返回数据");
                }
                if (data.code === 200) {
                    deferred.resolve(data);
                } else {
                    if (data.code === 301 && data.data !== null) {
                        $.pjax({url: data.data, container: '#app'});

                    } else if (data.code === 302 && data.data !== null) {
                        location.href = data.data;
                    }
                    if(data.msg!==""&&data.msg!==null&&data.msg!==undefined){
                        mdbAdmin.toast.error(data.msg);
                    }
                    deferred.reject(data);
                }

            },
            complete: function () {
                if (dom !== undefined) {
                    $.each(loadings, function (k, v) {
                        mdbAdmin.loading.hide(v);
                    });
                }
            },
            error: function (e) {
                log.error("请求错误", e);
                mdbAdmin.toast.error("网络错误");
                deferred.reject({'code': 500, 'msg': "网络错误"});
            }
        });
        return deferred.promise();
    },
    modal: {
        position:{
            topRight: "modal-side  modal-top-right",
            topLeft: "modal-side modal-top-left",
            bottomRight: "modal-side  modal-bottom-right",
            bottomLeft: "modal-side  modal-bottom-right",
            center:"modal-dialog-centered modal-dialog-scrollable"
        },
        size:{
            small:'modal-sm',
            default:'',
            large:'modal-lg',
            extraLarge:'modal-xl',
            full:'modal-fullscreen',
        },
        color:{
            // primary:"bg-primary",
            primary:["primary","white"],
            success:["success","white"],
            error:["danger","white"],
            info:["info","white"],
            warning:["warning","white"],
        },
        show(config) {
            config = $.extend({}, {
                title:'',
                body:'',
                position:this.position.center,
                size:this.size.default,
                color:this.color.primary,
                buttons: [
                    ['关闭'], ['确定']
                ],
            },config);
            app.loading = app.loading + 1;
            const id = "modal-" + app.loading.toString();
            var tpl = `
            <div class="modal fade" id="${id}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  ${config['position']} ${config['size']}">
    <div class="modal-content">
      <div class="modal-header bg-${config['color'][0]} text-${config['color'][1]}">
        <h5 class="modal-title " >${config['title']}</h5>
        <button type="button" class="btn-close btn-close-${config['color'][1]}" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">${config['body']}</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-${config['color'][0]}" >${config['buttons'][0][0]}</button>
        <button type="button" class="btn  btn-${config['color'][0]}" >${config['buttons'][1][0]}</button>
      </div>
    </div>
  </div>
</div>
            `;
            $("body").append(tpl);
            mdb.Modal.getOrCreateInstance(document.getElementById(id)).show();

            $("#"+id+" .modal-footer button:eq(0)").on("click",function () {
                let func = config.buttons[0][1];
                if(typeof func === "function"){
                    func(id);

                }
                mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
            });
            $("#"+id+" .modal-footer button:eq(1)").on("click",function () {
                let func = config.buttons[1][1];
                if(typeof func === "function"){
                    func(id);

                }
                mdb.Modal.getOrCreateInstance(document.getElementById(id)).hide();
            });
            resetTheme();
            document.getElementById(id).addEventListener('hidden.mdb.modal', () => {
                document.getElementById(id).remove();
            });
        },
    },
    alert: {
        success(msg) {
            this.custom(msg, 'success');
        },
        error(msg) {
            this.custom(msg, 'danger');
        },
        info(msg) {
            this.custom(msg, 'info');
        },
        warning(msg) {
            this.custom(msg, 'warning');
        },
        custom(msg, color) {
            app.loading = app.loading + 1;
            const id = "alert-" + app.loading.toString();
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
            mdb.Alert.getOrCreateInstance(document.getElementById(id)).show();
            resetTheme();
            document.getElementById(id).addEventListener('closed.mdb.alert', () => {
                document.getElementById(id).remove();
            });
        }
    },
    toast: {
        success(msg, title) {
            title = title || "成功";
            this.custom(msg, title, 'success');
        },
        error(msg, title) {
            title = title || "错误";
            this.custom(msg, title, 'danger');
        },
        info(msg, title) {
            title = title || "提示";
            this.custom(msg, title, 'info');
        },
        warning(msg, title) {
            title = title || "警告";
            this.custom(msg, title, 'warning');
        }, custom(msg, title, color) {
            app.loading = app.loading + 1;
            const id = "toast-" + app.loading.toString();
            const tpl = `
            
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
  data-mdb-width="350px"
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
            $("body").append(tpl);
            mdb.Toast.getOrCreateInstance(document.getElementById(id)).show();
            resetTheme();
            document.getElementById(id).addEventListener('hidden.mdb.toast', () => {
                document.getElementById(id).remove();
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
            mobile: android || ios || wechat || kindle,
        };
    },
    log: {
        info(msg, title) {
            this.pretty(title, msg, 'primary');
        },
        error(msg, title) {
            this.pretty(title, msg, 'danger');
        },
        success(msg, title) {
            this.pretty(title, msg, 'success');
        },
        warning(msg, title) {
            this.pretty(title, msg, 'warning');
        },
        typeColor(type = 'default') {
            let color = '';
            switch (type) {
                case 'primary':
                    color = '#2d8cf0';
                    break;
                case 'success':
                    color = '#19be6b';
                    break;
                case 'info':
                    color = '#909399';
                    break;
                case 'warning':
                    color = '#ff9900';
                    break;
                case 'danger':
                    color = '#f03f14';
                    break;
                default:
                    color = '#35495E';
                    break;
            }
            return color;
        },
        print(text, type, back) {
            back = back || false;
            if (typeof text === 'object') { // 如果是对象则调用打印对象方式
                console.dir(text);
                return;
            }
            if (back) { // 如果是打印带背景图的
                console.log(
                    `%c ${text} `,
                    `background:${this.typeColor(type)}; padding: 2px; border-radius: 4px;color: #fff;`
                );
            } else {
                console.log(
                    `%c ${text} `,
                    `color: ${this.typeColor(type)};`
                );
            }
        },
        pretty(title, text, type) {
            title = title || "Mdb Pro Admin";
            if (typeof text === 'object') { // 如果是对象则调用打印对象方式
                this.print(title, type, true);
                console.log(text);
                return;
            }
            console.log(
                `%c ${title} %c ${text} %c`,
                `background:${this.typeColor(type)};border:1px solid ${this.typeColor(type)}; padding: 1px; border-radius: 4px 0 0 4px; color: #fff;`,
                `border:1px solid ${this.typeColor(type)}; padding: 1px; border-radius: 0 4px 4px 0; color: ${this.typeColor(type)};`,
                'background:transparent'
            );
        },
    },
    database(config) {

        config = $.extend({},{
            elem:"#database",
            url:'',
            param:{},
            pageElm:"#pagination",
            rowClick:function () {

            },
            page:1,
            size:15,
            onsuccess:function () {

            },
            selectable:false,
            multi:false,
            onSelected:function () {

            },
            columns: [
                {label: 'ID', field: 'id',render:function () {}},
            ]
        },config);
        $.ajax({
            url: config['url'],  // 请求的URL
            method: 'GET',                  // 请求方法（GET、POST等）
            data: $.extend({},{ page: config['page'], size: config['size'] },config['param']),
            dataType: 'json',
            success: function(response) {
                if(response.code!==200){
                    if (response.code === 301 && response.data !== null) {
                        $.pjax({url: response.data, container: '#app'});
                        return;

                    } else if (response.code === 302 && response.data !== null) {
                        location.href = response.data;
                        return;
                    }

                    mdbAdmin.toast.error(response.msg);
                    return;
                }
                var raw = JSON.parse(JSON.stringify( response.data));

                mdb.Datatable.getOrCreateInstance(document.querySelector(config.elem)).update({
                        multi:config['multi'],
                        selectable:config['selectable'],
                        columns: config['columns'],
                        rows: $.map(raw, function (row, index) {
                            var item  = response.data[index];
                            $.each(config['columns'], function (k, v) {

                                let field = v['field'];
                                let func = v.hasOwnProperty('render') ? v.render : null;
                                if (typeof func === "function") {
                                    let result = func(item, index,response);
                                    if (result === undefined || result === null) {
                                        row[field] = item.hasOwnProperty(field) ? item[field] : '';
                                    } else {
                                        row[field] = result;
                                    }

                                }
                            });
                            return row;
                        })
                    },
                    {loading: false});



                $(config.elem)
                    .off('rowClick.mdb.datatable').off('selectRows.mdb.datatable')
                    .on('rowClick.mdb.datatable', function (e) {
                    const { index } = e;
                    if(index===null){
                        return;
                    }
                    const func = config['rowClick'];
                    if(typeof func === "function"){
                        func(response.data[index],config,response,raw[index]);
                    }
                })
                    .on('selectRows.mdb.datatable', function (e) {
                    console.log(e.selectedRows, e.selectedIndexes, e.allSelected);
                    const func = config['onSelected'];
                    if(typeof func === "function"){
                        var $array = [],$raw = [];
                        $.each(e.selectedIndexes,function (k,v) {
                            $array.push(response.data[v]);
                            $raw.push(raw[v]);
                        });
                        func($array,e.selectedIndexes,config,response,$raw);
                    }
                });

                new Pagination(document.querySelector(config.pageElm), {
                    current: config['page'],
                    total: response.count,
                    size: config['size'],
                    onPageChanged: (page) => {
                        config['page'] = page;
                        mdbAdmin.database(config);
                    }
                }).render();
                const func = config['onsuccess'];
                if(typeof func === "function"){
                    func(response.data,config,response);
                }
            },
            error: function (xhr, status, error) {
                log.error(error);
                mdbAdmin.toast.error("网络异常");
            }
        });


    },
    upload(config) {
        config = $.extend({}, {
            elem: "",
            url: '',
            dom: '',
            msg: '',
            onsuccess: function () {

            }
        }, config);
        var dom = config.dom || "#app";
        var msg = config.msg;
        var loadings = "";

        $(config.elem).off().on('fileAdd.mdb.fileUpload', function (e) {
            const addedFile = e.files;
            const data = new FormData();
            data.append('file', addedFile[0]);
            var that = this;
            $.ajax({
                type: 'POST',
                headers: {
                    Authorization: localStorage.getItem('Authorization') || ''
                },
                url: config.url,
                data: data,
                dataType: "json",
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    loadings = mdbAdmin.loading.show(dom, msg);
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    // 监听上传进度事件
                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            var percentComplete = event.loaded / event.total * 100;
                            $("#" + loadings).find(".loading-text").html(percentComplete.toFixed(2) + '%');
                        }
                    }, false);

                    return xhr;
                },
                error: function (error) {
                    log.error("请求错误", error);
                    mdbAdmin.toast.error("文件上传失败","网络错误");

                },
                success: function (ret) {
                    if (ret.code !== 200) {
                        mdbAdmin.toast.error( ret.msg,"文件上传失败");
                    } else {
                        mdbAdmin.toast.success("文件上传成功！");
                        sessionStorage.setItem("file_"+$(that).attr("name"),ret.data);
                        if (typeof config.onsuccess === "function") {
                            config.onsuccess(ret.data);
                        }
                    }
                },
                complete: function () {
                    mdbAdmin.loading.hide(loadings);
                }
            });
        });
    },
    form:{
        set(formElem, jsonData) {
            // 获取表单元素
            var formElements = $(formElem).find(":input:not(:reset):not(:button):not(:submit)[name]");
            // 参数设置
            mdbAdmin.form.reset(formElem);
            // 传入的json数据非空时
            if (jsonData && $.type(jsonData) === 'object' && !$.isEmptyObject(jsonData)) {



// 使用示例


                // 遍历容器内所有表单元素
                formElements.each(function () {
                    // 表单元素名称
                    var name = $(this).attr("name");
                    // jsonData key 值（移除 name 的前缀和后缀）
                    var key = name;

                    if (jsonData.hasOwnProperty(key)) {
                        if ($(this).is(":file")) {
                             sessionStorage.setItem("file_"+name,jsonData[key] );
                             if(hasLoadPlugin(mdbAdminPlugins["file-upload"])){
                                 FileUpload.getInstance(this).update({"defaultFile":jsonData[key]});
                             }

                        }
                        // 当表单元素为 radio 时
                        else if ($(this).is(":radio")) {
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
                            $(this).val(jsonData[key]).focus().blur();
                            if($(this).is("select")){
                                mdb.Select.getOrCreateInstance($(this).get(0)).setValue(jsonData[key]);
                            }

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
                    jsonData[key]  = sessionStorage.getItem("file_"+name);
                }
                // 当为 checkbox 时
                else if ($(this).is(":checkbox")) {
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
            if(hasLoadPlugin(mdbAdminPlugins["file-upload"])){
                document.querySelectorAll("[type='file']").forEach(function (k) {
                    var instance = FileUpload.getInstance(k);
                    if(instance===null){
                        instance = new FileUpload(k);
                    }
                    instance.update({"defaultFile":""});
                });

            }
            return $(formElem);
        },
        submit(formElem, fn) {
            $(formElem).on("submit",function () {
                if (typeof fn === "function") {
                    fn(mdbAdmin.form.get(formElem));
                }
                return false;
            });
        },
        init(formElem,url,init_success,submit_success){
            this.bindInit(formElem,url,init_success);
            this.bindSubmit(formElem,url,submit_success);
        },
        bindInit(formElem,url,init_success){
            mdbAdmin.request(url,{},"GET",{"#app":"获取数据中..."}).done(function (data) {
                form.val(formElem,data.data);
                if( typeof init_success === "function"){
                    init_success(data.data);
                }
            });
        },
        bindSubmit(formElem,url,submit_success){
            form.submit(formElem,function (d) {
                mdbAdmin.request(url,d,"POST",{"#app":"正在修改中"}).done(function (data) {
                    mdbAdmin.toast.success(data.msg);
                    if(typeof  submit_success === "function"){
                        submit_success(d);
                    }
                });
            });
        }
    },
    dateFormat (fmt, date) {
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
        if (/(y+)/.test(fmt)){
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        }

        for (const k in o){
            if (new RegExp("(" + k + ")").test(fmt)){
                fmt = fmt.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }

        }
        return fmt;
    },

};
const form = mdbAdmin.form;

/* jshint -W003 */
const log = mdbAdmin.log;
/* jshint +W003 */
log.print('      ___          _____                  \n' +
    '     /__/\\        /  /::\\        _____    \n' +
    '    |  |::\\      /  /:/\\:\\      /  /::\\   \n' +
    '    |  |:|:\\    /  /:/  \\:\\    /  /:/\\:\\  \n' +
    '  __|__|:|\\:\\  /__/:/ \\__\\:|  /  /:/~/::\\ \n' +
    ' /__/::::| \\:\\ \\  \\:\\ /  /:/ /__/:/ /:/\\:|\n' +
    ' \\  \\:\\~~\\__\\/  \\  \\:\\  /:/  \\  \\:\\/:/~/:/\n' +
    '  \\  \\:\\         \\  \\:\\/:/    \\  \\::/ /:/ \n' +
    '   \\  \\:\\         \\  \\::/      \\  \\:\\/:/  \n' +
    '    \\  \\:\\         \\__\\/        \\  \\::/   \n' +
    '     \\__\\/                       \\__\\/    \n' +
    '      ___         ___           ___     \n' +
    '     /  /\\       /  /\\         /  /\\    \n' +
    '    /  /::\\     /  /::\\       /  /::\\   \n' +
    '   /  /:/\\:\\   /  /:/\\:\\     /  /:/\\:\\  \n' +
    '  /  /:/~/:/  /  /:/~/:/    /  /:/  \\:\\ \n' +
    ' /__/:/ /:/  /__/:/ /:/___ /__/:/ \\__\\:\\\n' +
    ' \\  \\:\\/:/   \\  \\:\\/:::::/ \\  \\:\\ /  /:/\n' +
    '  \\  \\::/     \\  \\::/~~~~   \\  \\:\\  /:/ \n' +
    '   \\  \\:\\      \\  \\:\\        \\  \\:\\/:/  \n' +
    '    \\  \\:\\      \\  \\:\\        \\  \\::/   \n' +
    '     \\__\\/       \\__\\/         \\__\\/    \n' +
    '      ___          _____          ___                       ___     \n' +
    '     /  /\\        /  /::\\        /__/\\        ___          /__/\\    \n' +
    '    /  /::\\      /  /:/\\:\\      |  |::\\      /  /\\         \\  \\:\\   \n' +
    '   /  /:/\\:\\    /  /:/  \\:\\     |  |:|:\\    /  /:/          \\  \\:\\  \n' +
    '  /  /:/~/::\\  /__/:/ \\__\\:|  __|__|:|\\:\\  /__/::\\      _____\\__\\:\\ \n' +
    ' /__/:/ /:/\\:\\ \\  \\:\\ /  /:/ /__/::::| \\:\\ \\__\\/\\:\\__  /__/::::::::\\\n' +
    ' \\  \\:\\/:/__\\/  \\  \\:\\  /:/  \\  \\:\\~~\\__\\/    \\  \\:\\/\\ \\  \\:\\~~\\~~\\/\n' +
    '  \\  \\::/        \\  \\:\\/:/    \\  \\:\\           \\__\\::/  \\  \\:\\  ~~~ \n' +
    '   \\  \\:\\         \\  \\::/      \\  \\:\\          /__/:/    \\  \\:\\     \n' +
    '    \\  \\:\\         \\__\\/        \\  \\:\\         \\__\\/      \\  \\:\\    \n' +
    '     \\__\\/                       \\__\\/                     \\__\\/    \n' +
    'V1.0 Powered by Ankio', 'primary', false);




