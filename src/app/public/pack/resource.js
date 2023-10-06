var loadPlugins = [];
var  loadCss = [];
var loadJs = [];
var dir = "";
function getPath() {
    if (dir !== "") {
        return dir;
    }
    const jsPath = window.document.currentScript
        ? window.document.currentScript.src
        : (function() {
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
    return dir = jsPath.substring(0, jsPath.lastIndexOf("/") + 1);
}
function hasLoadPlugin(plugin) {
    return loadPlugins.includes(plugin);
}

var resource  = {
    cssAll(hrefs){
        const deferred = $.Deferred();
        if(!hrefs ||hrefs.length===0){
            deferred.resolve();
            return deferred.promise();
        }
        if (typeof hrefs === "string") {
            hrefs = [hrefs];
        }
        // 创建一个用于存放所有加载操作的数组
        const allFiles = [];
        hrefs.forEach(function(path) {
            allFiles.push(this.css(path));
        });
        $.when.apply($, allFiles).done(function() {
            deferred.resolve();
        });
        return deferred.promise();
    },
    css(href) {
        const deferred = $.Deferred();
        if(loadCss.indexOf(href)!==-1 || !href){
            deferred.resolve();
        }else{
            loadCss.push(href);

            $("<link>", {
                rel: "stylesheet",
                type: "text/css",
                href: href
            }).appendTo("head").on("load", function() {
                deferred.resolve();
            });
        }

        return deferred.promise();
    },
    jsAll(hrefs){
        const deferred = $.Deferred();
        if(!hrefs || hrefs.length===0){
            deferred.resolve();
            return deferred.promise();
        }
        if (typeof hrefs === "string") {
            hrefs = [hrefs];
        }
        // 创建一个用于存放所有加载操作的数组
        const allFiles = [];
        hrefs.forEach(function(path) {
            allFiles.push(resource.js(path));
        });
        $.when.apply($, allFiles).done(function() {
            deferred.resolve();
        });
        return deferred.promise();
    },
    js(js) {
        if(loadJs.indexOf(js)!==-1) {
            const deferred = $.Deferred();
            deferred.resolve();
            return deferred.promise();
        }
        loadJs.push(js);


        return $.getScript(js);

    },
    use(models, fn) {
        if(models.length===0){
            fn();
            return;
        }
        if (typeof models === "string") {
            models = [models];
        }
        // 创建一个用于存放所有加载操作的数组
        const allFiles = [];
        models.forEach(function(path) {
            if(loadPlugins.indexOf(path)!==-1)return;
            loadPlugins.push(path);
            allFiles.push($.getScript(getPath() + "mdb/plugins/js/" + path + ".min.js"));
            allFiles.push(resource.css(getPath() + "mdb/plugins/css/" + path + ".min.css"));
        });
        $.when.apply($, allFiles).done(function() {
            fn();
        });
    },
};