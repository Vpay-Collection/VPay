let config;
String.prototype.format = function () {
    //字符串占位符
    //eg: var str1 = "hello {0}".format("world");
    if (arguments.length === 0) return this;
    const param = arguments[0];
    let s = this;
    if (typeof (param) == 'object') {
        for (const key in param) {
            s = s.replace(new RegExp("\\{" + key + "\\}", "g"), param[key]);
        }
        return s;
    } else {
        for (let i = 0; i < arguments.length; i++) {
            s = s.replace(new RegExp("\\{" + i + "\\}", "g"), arguments[i]);
        }
        return s;
    }
};
layui.define(["element", "jquery", "utils","request","route","okConfig"], function (exports) {
    let okTab = function () {
        this.tabConfig = {
            tabFilter: "ok-tab", //添加窗口的filter
            url: "", //获取菜单的接口地址
            data: [],//菜单数据列表(如果传入了url则data无效)
            parseData: function () {

            }//这是一个方法处理url请求地址的返回值(该方法必须提供一个返回值)
        }
    };
    okTab.prototype.data={};
    const layui = parent.layui || layui;
    const okUtils = layui.utils;
    const $ = layui.jquery;
    const element = layui.element,
        layer = layui.layer;
    const route = layui.route;

    /**打开缓存中的tabMenu**/
    let OpenTabMenuFun = function ($, callback) {

            let tabMenu = sessionStorage.getItem("tabMenu");//已经打开的tab页面
            if (tabMenu) {
                tabMenu = JSON.parse(tabMenu);
                $("#tabTitle").html(tabMenu.tabTitle);
               // $("#tabContent").html(tabMenu.tabContent);
                if (typeof callback == 'function') {
                    callback();
                }
              //  route.go(okTab.prototype.data[okUtils.session("lay-id")]);

        }

    };

    /**删除缓存中的tabMenu**/
    let removeTabMenu = function (okTab, callback) {
        sessionStorage.removeItem("tabMenu");
        sessionStorage.removeItem("notice");
        sessionStorage.removeItem("lay-id");
        if (typeof callback == "function") {
            callback(okTab);
        }
    };

    /**存储打开的tabMenu**/
    function saveTabMenuFun($) {
            let tabTitle = $("<div>" + $("#tabTitle").html() + "</div>");
            tabTitle.find("i.layui-tab-close").remove();
            let tabMenu = JSON.stringify({
                tabTitle: tabTitle.html(),
                //tabContent: $("#tabContent").html()
            });
            sessionStorage.setItem('tabMenu', tabMenu);

    }
    //注册路由数据
    config = okUtils.local("config")  || {};
    route.register(config.routes).init("#tabContent","#bindHomeScript").refresh();//刷新当前页面路由信息
  //  okUtils.session("tabMenu", null);
   // okUtils.session("lay-id", null);

    /**
     * 导航初始化的操作(只执行一次)
     * @param option 配置tabConfig参数
     * @returns {okTab}
     */
    okTab.prototype.init = function (option) {
        const _this = this; //这个this就是代表okTab（因为prototype的原因）
        $.extend(true, _this.tabConfig, option); //函数用于将一个或多个对象的内容合并到目标对象。http://www.runoob.com/jquery/misc-extend.html
        this.tabDelete(); //关闭导航页的操作
        this.tab(); //tab导航切换时的操作
        return _this;
    };

    //生成左侧菜单
    let temp = "";
    okTab.prototype.navBar = function (strData) {
        let data;
        if (typeof (strData) == "string") {
             data = JSON.parse(strData); //有可能是字符串，转换一下
        } else {
            data = strData || [];
        }
        let ulItem = '';
        for (let i = 0; i < data.length; i++) {
            temp = "";
            if (data[i].isCheck) {
                ulItem += '<li class="layui-nav-item layui-this">';
            } else if (data[i].spread) {
                ulItem += '<li class="layui-nav-item layui-nav-itemed">';
            } else {
                ulItem += '<li class="layui-nav-item">';
            }
            createMenu(data[i], (i + 1).toString());
            ulItem += temp;
            ulItem += '</li>';
        }
        return ulItem;
    };

    /**
     * 递归生成菜单
     * @param data 数据
     * @param tabID 设置lay-id使用
     */
    function createMenu(data, tabID) {
        if (data.children !== undefined && data.children.length > 0) {
            temp += '<a>';
            if (data.icon !== undefined && data.icon !== '') {
                if (!data.fontFamily || data.fontFamily.indexOf("layui-icon") >= 0) {
                    if (data.icon.indexOf("&#") >= 0) {
                        temp += ('<i class="layui-icon">{0}</i>').format(data.icon);
                    } else {
                        temp += ('<i class="layui-icon {0}"></i>').format(data.icon);
                    }
                } else if (data.fontFamily) {
                    if (data.icon.indexOf("&#") >= 0) {
                        temp += ('<i class="{0}">{1}</i>').format(data.fontFamily, data.icon);
                    } else {
                        temp += ('<i class="{0} {1}"></i>').format(data.fontFamily, data.icon);
                    }
                }
            }
            temp += '<cite>' + data.title + '</cite>';
            temp += '<span class="layui-nav-more"></span>';
            temp += '</a>';
            temp += '<dl class="layui-nav-child">';
            for (var i = 0; i < data.children.length; i++) {
                temp += '<dd>';
                var lay_id = tabID + "-" + (i + 1);
                createMenu(data.children[i], lay_id);
                temp += '</dd>';
            }
            temp += "</dl>";
        } else {
            const isClose = data.isClose === false ? "false" : "true";
            if (data.target) {
                temp += ('<a lay-id="{0}" data-url="{1}" target="{2}" is-close="{3}">').format(tabID, data.href, data.target, isClose);
            } else {
                temp += ('<a lay-id="{0}" data-url="{1}" is-close="{2}">').format(tabID, data.href, isClose);
            }
            okTab.prototype.data[tabID]=data.href;

            if (data.icon !== undefined && data.icon != '') {
                if (!data.fontFamily || data.fontFamily.indexOf("layui-icon") >= 0) {
                    if (data.icon.indexOf("&#") >= 0) {
                        temp += ('<i class="layui-icon">{0}</i>').format(data.icon);
                    } else {
                        temp += ('<i class="layui-icon {0}"></i>').format(data.icon);
                    }
                } else if (data.fontFamily) {
                    if (data.icon.indexOf("&#") >= 0) {
                        temp += ('<i class="{0}">{1}</i>').format(data.fontFamily, data.icon);
                    } else {
                        temp += ('<i class="{0} {1}"></i>').format(data.fontFamily, data.icon);
                    }
                }
            }
            temp += ('<cite>{0}</cite></a>').format(data.title);

        }
    }

    /**
     * 定位tab位置
     * @param contEle
     * @param ele 当前tab
     */
    okTab.prototype.positionTab = function (contEle, ele) {

        const superEle = $(".ok-tab");//父级元素
        contEle = contEle ? $(contEle) : $("#tabTitle");//(contEle);//tab内容存放的父元素
        ele = ele ? $(ele) : $("#tabTitle li.layui-this");//当前的tab

        const menuSet = $("#navBar a[lay-id]");//获取所有菜单集合
        const thatLayId = ele.attr('lay-id');

        let contWidth = contEle.width(),//父级元素宽度
            superWidth = parseInt(superEle.width()) - 40 * 3,//可显示的宽度
            subWidth = ele.outerWidth(),//当前元素宽度
            elePrevAll = ele.prevAll(),//左边的所有同级元素
            leftWidth = 0,//左边距离
            postLeft = Math.abs(contEle.position().left);//当前移动的位置
        const maxMoveWidth = contWidth - superWidth;//最大可移动的宽度

        for (let i = 0; i < elePrevAll.length; i++) {
            leftWidth += $(elePrevAll[i]).outerWidth() * 1;
        }
        if (contWidth > superWidth) {
            /**
             * 移动tab栏的位置
             */
            const showPost = leftWidth - postLeft;//当前点击的元素位置（显示区域的位置）
            const halfPlace = parseInt(superWidth / 2);//可显示的宽度的一半
            let tempMove = 0;
            if (halfPlace < showPost) {//从右往左移动
                tempMove = leftWidth - subWidth;//预留一部分距离
                if (tempMove > maxMoveWidth) {//当前移动的距离超过最大可移动的宽度
                    tempMove = maxMoveWidth;
                }
                contEle.animate({
                    left: -tempMove
                }, 50);
            } else {//从左往右移动
                tempMove = leftWidth - halfPlace;//预留一部分距离
                if (tempMove < 0) {
                    tempMove = 0;
                }
                contEle.animate({
                    left: -tempMove
                }, 50);
            }
        } else {
            contEle.animate({
                left: 0
            }, 50);
        }

        /**
         * 左侧菜单的样式和多级菜单的展开
         */
        $("#navBar").find("li,dd").removeClass("layui-this").removeClass("layui-nav-itemed");//关闭所有展开的菜单
        $("#navBar > li dl.layui-nav-child").removeAttr('style');
        for (let i = 0; i < menuSet.length; i++) {
            if ($(menuSet[i]).attr('lay-id') === thatLayId) {
                $(menuSet[i]).parents("dd").addClass("layui-nav-itemed");
                $(menuSet[i]).parents("li").addClass("layui-nav-itemed");
                $(menuSet[i]).parent().removeClass("layui-nav-itemed").addClass("layui-this");
                break;
            }
        }
    };

    // 点击导航页的操作
    okTab.prototype.tab = function (e) {
        const that = this;
        const filter = this.tabConfig.tabFilter;
        element.on("tab({0})".format(filter), function (data) {
            const index = data.index;//点击的某个tab索引
            config = okUtils.local("config") || {};
            sessionStorage.setItem('lay-id', this.getAttribute('lay-id'));
            const elSuper = $(".ok-tab"),//视窗元素
                elMove = $(".ok-tab-title"),//移动的元素
                elTabs = $(".ok-tab-title li"),//所有已存在的tab集合
                thatElem = $(elTabs[index]);//当前元素
            route.go(that.data[$(this).attr("lay-id")]);
            saveTabMenuFun($);
            that.positionTab(elMove, thatElem);
        });
    };

    //删除tab页的操作（此处为点击关闭按钮的操作）
    okTab.prototype.tabDelete = function () {
        const filter = this.tabConfig.tabFilter;
        element.on("tabDelete({0})".format(filter), function (data) {
            saveTabMenuFun($);
        });
    };

    //删除缓存中的tab
    okTab.prototype.removeTabStorage = function (callback) {
        removeTabMenu(this, callback);
    };

    //添加tab页
    okTab.prototype.tabAdd = function (_thisa) {
        const that = this;
        const _this = $(_thisa).clone(true);//拷贝dom（js： _this.cloneNode(true) ）
        const openTabNum = that.tabConfig.openTabNum;
        const tabFilter = that.tabConfig.tabFilter;
        const url = _this.attr("data-url");//选项卡的页面路径
        const tabId = _this.attr("lay-id");//选项卡ID
        const thatTabNum = $('.ok-tab-title > li').length;//当前已经打开的选项卡的数量
        this.data[tabId]=url;
        route.go(url,function (bindView,bindScript) {
            $(bindView).attr("lay-id",tabId);
            $(bindView).attr("data-url",url)
        });
        const isClose = _this.attr('is-close') || "true";

        _this.prepend("<strong style='display: none;' is-close=" + isClose + " lay-id=" + tabId + "></strong>");

        if (_this.children("i").length < 1) {
            _this.prepend("<i class='layui-icon'></i>");
        }
        if (_this.attr("target") === "_blank") {
            window.location.href = _this.attr("data-url");
        } else if (url !== undefined) {
            const html = _this.html();
            for (let i = 0; i < thatTabNum; i++) {
                if ($('.ok-tab-title > li').eq(i).attr('lay-id') === tabId) {
                    route.refresh();
                    element.tabChange(tabFilter, tabId);
                    return;
                }
            }

            route.go(url)
            element.tabAdd(tabFilter, {
                title: html,
                id: tabId
            });
            // 切换选项卡
            element.tabChange(tabFilter, tabId);
            this.navMove("rightmax");
        }
    };

    //重新对导航进行渲染(此处有个回调函数，主要用作渲染完成之后的操作)
    okTab.prototype.render = function (callback) {
        const _this = this;//data
        let _data = _this.tabConfig.data;
        if (_this.tabConfig.url) {
            layui.request.call(_this.tabConfig.url, "post",{},"#app","正在加载组件...").done(function (res) {
                _data = res.data;
                if (typeof _this.tabConfig.parseData == "function") {
                    _data = _this.tabConfig.parseData(res.data);
                }
                $("#navBar").html(_this.navBar(_data));
                element.render('nav');
                $(window).resize(function () {
                    $("#navBar").height($(window).height() - 245);
                });
                if (typeof callback == "function") {
                    callback();
                }
                var lay_id = sessionStorage.getItem('lay-id');
                element.tabChange(_this.tabConfig.tabFilter, lay_id);

            }).fail(function (error) {
                console.log(error)
                /*   localStorage.setItem("token",null)
                   window.location = "/pages/login.html";*/
            });
            /* $.get(_this.tabConfig.url, function (res) {

             });*/
            /**打开缓存中的tab**/
            OpenTabMenuFun($, function () {
                // var filter = _this.tabConfig.tabFilter;
                element.render("tab");
            });
        } else if ($.type(_data) === 'array') {
            if (_data.length < 1) {
                alert("菜单集合中无任何数据");
            }
            _data = _this.tabConfig.data;
            $("#navBar").html(_this.navBar(_data));
            element.render('nav');
            $(window).resize(function () {
                $("#navBar").height($(window).height() - 245);
            });
            if (typeof callback == "function") {
                callback();
            }
        } else {
            alert("你的菜单配置有误请查看菜单配置说明");
        }
    };

    //刷新当前tab页
    okTab.prototype.refresh = function (_this, callback) {
        if (!($(_this).hasClass("refreshThis"))) {
            $(_this).addClass("refreshThis");
            route.refresh();
            if (typeof callback == "function") {
                callback(okTab);
            }
            setTimeout(function () {
                $(_this).removeClass("refreshThis");
            }, 2000)
        } else {
            layer.msg("客官请不要频繁点击哦！我会反应不过来的");
        }
    };

    /**
     * 关闭tab标签页操作
     * @param num 默认为1
     * 1：代表关闭当前标签页
     * 2：代表关闭其他标签页
     * 3：代表关闭所有标签页
     */
    okTab.prototype.tabClose = function (num) {
        num = num || 1;
        num = num * 1;//强制转换成数字
        const that = this;
        let openTabs = $('.ok-tab-title > li strong[lay-id]').not('strong[is-close=false]'),//获取已打开的tab元素(除开不会被关闭的导航)
            thatLayID = $('.ok-tab-title > li.layui-this strong[lay-id]').not('strong[is-close=false]').attr("lay-id") || '';//获取当前打开的tab元素ID(除开不会被关闭的导航)

        const filter = that.tabConfig.tabFilter;
        if (thatLayID.length < 1 && num == 1) {
            layer.msg("您不能关闭当前页哦 (๑╹◡╹)ﾉ");
            return;
        } else if (openTabs.length < 1) {
            layer.msg("您好！当前没有可关闭的窗口了 (๑╹◡╹)ﾉ");
            return;
        }
        switch (num) {
            case 1:
                element.tabDelete(filter, thatLayID);
                break;
            case 2:
                if (openTabs.length > 0) {
                    openTabs.each(function (i, j) {
                        if ($(j).attr("lay-id") !== thatLayID) {
                            element.tabDelete(filter, $(j).attr("lay-id"));
                        }
                    });
                    this.navMove('leftmax');
                } else {
                    layer.msg("您好！当前没有可关闭的窗口了 (๑╹◡╹)ﾉ");
                    return;
                }
                break;
            case 3:
                openTabs.each(function (i, j) {
                    element.tabDelete(filter, $(j).attr("lay-id"));
                });
                this.navMove('leftmax');
                break;
        }
        element.render("tab", filter);
        /**保存展开的tab**/
        saveTabMenuFun($);
    };

    /**
     * 移动标签导航
     * @param moveId
     *      为left:往左边移动
     *      为right:往右边移动
     *      为rightmax:跑到最右边
     *      为leftmax:跑到最左边
     * @param that
     */
    okTab.prototype.navMove = function (moveId, that) {
        const superEle = $(".ok-tab"),//父级元素
            contEle = $(".ok-tab-title"),//tab内容存放的父元素
            superWidth = parseInt(superEle.width()) - 40 * 3,//可显示的宽度
            contWidth = parseInt(contEle.width()),//移动元素的总宽度
            elTabs = $(".ok-tab-title li"),//所有已存在的tab集合
            postLeft = contEle.position().left;//当前移动的位置

        /*elTabs.each(function (i, j) {
           moveWidth += $(j).outerWidth() * 1;
        });*/

        const movePost = moveId.toLowerCase().indexOf("left") < 0 ? -1 : 1;//移动方向
        const step = parseInt((superWidth * 0.25 < 20 ? 20 : superWidth * 0.25));//移动步长
        let moveLeft = postLeft + step * movePost;
        const moveWidth = contWidth - superWidth;
        if (contWidth > superWidth) {
            switch (moveId) {
                case 'left':
                    if (moveLeft >= step) {
                        layer.tips('已到最左边啦', that, {
                            tips: [1, '#000'],
                            time: 2000
                        });
                        moveLeft = 0;
                    }
                    break;
                case 'leftmax':
                    if (moveLeft >= step) {
                        layer.tips('已到最左边啦', that, {
                            tips: [1, '#000'],
                            time: 2000
                        });
                        moveLeft = 0;
                    }
                    break;
                case 'right':
                    if (superWidth + Math.abs(moveLeft) >= contWidth + step) {
                        layer.tips('已到最右边啦', that, {
                            tips: [1, '#000'],
                            time: 2000
                        });
                        moveLeft = -moveWidth;
                    }
                    if (superWidth + Math.abs(moveLeft) >= contWidth) {
                        moveLeft = -moveWidth;
                    }
                    break;
                case 'rightmax':
                    if (superWidth + Math.abs(postLeft) >= contWidth + step) {
                        layer.tips('已到最右边啦', that, {
                            tips: [1, '#000'],
                            time: 2000
                        });
                    }
                    moveLeft = -moveWidth;
            }
            if (moveLeft > 0) {
                moveLeft = 0;
            }
            contEle.animate({
                left: moveLeft
            }, 50);
        } else {
            contEle.animate({
                left: 0
            }, 50);
        }
    };

    exports("tab", function (option) {
        if (parent.objOkTab) {
            return parent.objOkTab;
        } else {
            return new okTab().init(option);
        }
    });


});

