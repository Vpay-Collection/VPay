if (!Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {
        if (typeof this !== "function") {
            throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
        }
        var aArgs = Array.prototype.slice.call(arguments, 1),
            fToBind = this,
            fNOP = function () { },
            fBound = function () {
                return fToBind.apply(this instanceof fNOP && oThis
                        ? this
                        : oThis,
                    aArgs.concat(Array.prototype.slice.call(arguments)));
            };
        fNOP.prototype = this.prototype;
        fBound.prototype = new fNOP();
        return fBound;
    };
}
layui.define(["jquery","loading"], function(exports) {
    const $ = layui.$;
    const route = {
        // 关键词
        key: '',
        bindView: "#body",
        bindScript: "#bindScript",
        // 路由表
        routes: [],
        // 当前 hash
        currentHash: '',
        beforeCallback: function (path) {
            return true
        },
        // 注册路由
        register: function (hash) {
            const that = this;
            // 路由数组
            for (const i in hash) {
                hash[i].page = hash[i].page || hash[i].path;
                that.routes[hash[i].path] = [hash[i].page, hash[i].callback || function () {
                }];
              //  console.log(that.routes)
            }
            return that;
        },
        go: function (hash,callback) {
            hash = hash || ""
            const that = this;
            if (hash.startsWith("http")) {
                window.location.open(hash)
            } else window.location.hash = '#'.concat(that.key).concat(hash || '');

            if(callback){
                callback(this.bindView,this.bindScript);
            }
            return that;
        },
        done:function () {

        },
        // 刷新
        refresh: function () {
            const that = this;
            // 获取相应的 hash 值
            // 如果存在 hash 则获取, 否则为 /
            that.currentHash = location.hash.slice(1) || '/';
            if (that.before(that.currentHash)) {
                if (that.routes[that.currentHash] != null) {
                    // 根据当前 hash 调用对应的回调函数
                    that.loadPage(that.routes[that.currentHash][0], that.bindView, that.bindScript)
                    that.routes[that.currentHash][1]();
                } else {
                    that.loadPage(that.currentHash, that.bindView, that.bindScript)
                }
            }

        },
        setBeforeCallback: function (fun) {
            this.beforeCallback = fun;
        },
        before: function (path) {
            if (this.beforeCallback !== undefined)
                return this.beforeCallback(path);
            return true;
        },
        // 初始化
        init: function (view, script) {
            const that = this;
            that.bindView = view || that.bindView;
            that.bindScript = script || that.bindScript;
            window.addEventListener('load', that.refresh.bind(that), false);
            window.addEventListener('hashchange', that.refresh.bind(that), false);
            return that;
        },
        // Removes the current page from the session history and navigates to the given URL.
        replace: function (url) {
            window.location.replace(url);
        },
        // Navigate to the given URL.
        href: function (url) {
            window.location.href = url;
        },
        // Reloads the current page.
        reload: function () {
            window.location.reload();
        },
        param:function (name) {
            if(window.location.href.indexOf("?")===-1)return null;
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.href.substr(window.location.href.indexOf("?")+1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]);
            return null; //返回参数值
        },
        loadPage: function (page, bindView, bindScript) {
            bindView = bindView || "#app";
            bindScript = bindScript || "#script";
            $(bindView).html("");
            $(bindScript).html();
            layui.loading.show("页面加载中...", bindView);
            var param = "";
            if(page.indexOf("?")!==-1){
               var p = page.split("?");
               page = p[0];
               param = "?"+p[1];
            }
            if(page==="")return
            $.ajax({
                url: "./pages/" + page + ".html"+param,
                async: false,
                dataType: "html",
                success: function (data) {
                   // layui.loading.close(bindView);
                    //<!--app-->
                    const pattern = /<script[\s\S]*?<\/script>/g;
                    const reg = data.match(pattern);
                    //console.log()
                    //console.log(pattern.exec(data));
                    let js = "";
                    if (reg != null ) {
                        let i;
                      //  console.log(reg);
                        for(i=0 ;i< reg.length;i++){
                            if(reg[i].indexOf("text/html")!==-1)
                                continue
                            data = data.replace(reg[i], "");
                          // console.log(reg[i]);
                            js += reg[i];
                        }

                    }
                    $(bindView).append(data);
                    $(bindScript).html(js);
                    layui.loading.close(bindView);
                },error:function () {
                    $.ajax({
                        url: "/pages/404.html",
                        async: false,
                        dataType: "html",
                        success: function (data) {
                            $(bindView).append(data);
                            layui.loading.close(bindView);
                        }
                    });
                }
            });
        }
    };
    exports('route', route);
});
