var routes = [];
var frames = [];
var routeSegmentation = "#!";

var ignoreHashChange = false;
var titlePrefix = "Ankioの用户中心"

function jsonToQueryString(json) {
    var pairs = $.map(json, function (value, key) {
        return encodeURIComponent(key) + '=' + encodeURIComponent(value);
    });
    return pairs.join('&');
}

function go(path, params) {
    var query = params ? "?" + jsonToQueryString(params) : "";
    location.href = routeSegmentation + path + query;
}

/**
 * 查询提取成数组
 * @return {{value: string, key: string}[]|*[]}
 */
function getQueryAsArray() {
    var position = location.hash.indexOf("?");

    if (position === -1) {
        return [];
    }

    var query = location.hash.substring(position);
    // 检查是否有查询参数

    // 去掉问号并按&分隔
    var queryParts = query.slice(1).split("&");
    var objects = {};
    $.each(queryParts, function (k, part) {
        var split = part.split("=");
        var key = decodeURIComponent(split[0]);
        objects[key] = decodeURIComponent(split[1]);
    });
    return objects;
}

function getHash(route) {
    route = route || "";
    var position = route.indexOf("?");

    if (position !== -1) {
        route = route.substring(0, position);
    }
    return route;
}

/**
 * 获取route handler
 * @return {{
 *     depends: string,
 *     container: string,
 *     reference: string,
 *     parent: string,
 *     title: string,
 *     onexit: function,
 *     onenter: function(array,string,string),
 *   }}
 */
function getRouter(route) {
    var routeHandler = null;
    route = getHash(route);
    $.each(routes, function (key, value) {
        if (typeof value.path === "string") {
            // 普通字符串匹配
            if (value.path === route) {
                routeHandler = value;
                return false;
            }
        } else {
            // 正则表达式匹配
            var match = route.match(value.path);
            if (match) {
                routeHandler = value;
                return false;
            }
        }
    });
    return routeHandler;
}

/**
 * 从url获取hash
 * @param url
 * @return {*|string}
 */
function getHashFromURL(url) {
    if (!url) return "";
    var hashIndex = url.indexOf(routeSegmentation);
    return hashIndex !== -1 ? url.slice(hashIndex + 2) : "/";
}

/**
 * 进行路由
 * @param event
 */
function router(event) {
    if (event.originalEvent) {
        event = event.originalEvent;
    }

    if (ignoreHashChange) {
        ignoreHashChange = false;
        return;
    }
    var oldHash = getHashFromURL(event.oldURL);
    var newHash = getHashFromURL(event.newURL);
    var newHandler = getRouter(newHash);
    var oldHandler = getRouter(oldHash);

    console.log("旧:", event.oldURL, "新", event.newURL);


    if (oldHandler && typeof oldHandler.onexit === "function") {
        try {
            oldHandler.onexit();
        } catch (e) {
            console.error(e);
        }
    }

    if (newHandler) {

        function pathHtml(handler, html, data) {
            var container = handler.container || "#app";
            if (handler.reference && $(container).length === 0) {
                router({
                    oldURL: null,
                    newURL: routeSegmentation + handler.reference,
                });
            }
            $.wait(container, function () {
                var dom = this;
                dom.fadeOut(function () {
                    if (handler.title) {
                        $("title").html(titlePrefix + " - " + handler.title);
                    }

                    var htmlDom = $("<div>" + html + "</div>");
                    var result = handler.onenter(getQueryAsArray(), htmlDom, data);
                    if (!result) {
                        dom.html(htmlDom);
                    }
                    setTimeout(function () {
                        if (typeof handler.onrender === "function") {
                            handler.onrender(getQueryAsArray(), htmlDom, data);
                        }
                        mdbAdmin.initComponents(container)
                    }, 0);
                    dom.fadeIn();
                });

            }, true);
        }

        var dependsDefer = $.Deferred();
        if (newHandler.depends) {

            request(newHandler.depends, getQueryAsArray(), newHandler.loading).done(function (result) {

                dependsDefer.resolve(result);
            }).fail(function (result) {

                dependsDefer.resolve(result);
            });
        } else {
            dependsDefer.resolve(null);
        }

        var htmlDefer = $.Deferred();

        loadHtml(newHash, function (html) {
            htmlDefer.resolve(html);
        });

        var libsDefer = $.Deferred();

        if (newHandler.libs) {

            resource.use(newHandler.libs, function () {

                libsDefer.resolve();
            })
        } else {

            libsDefer.resolve();
        }

        var id = null;

        if (newHandler.loading) {
            id = mdbAdmin.loading.show("body", newHandler.loading);
        }

        $.when(dependsDefer, htmlDefer, libsDefer, resource.jsAll(newHandler.js), resource.cssAll(newHandler.css)).done(function (result, html) {
            if (id) mdbAdmin.loading.hide(id);
            pathHtml(newHandler, html, result);
        });


    }else{
		go("error");
	}
}

function loadHtml(newHash, onload) {
    var hash = getHash(newHash);
    var html = sessionStorage.getItem("page_" + hash);
    if (!window.debug && html) {
        onload(html);
    } else {
        $.get(("pages/" + hash).replace("//", "/"), function (data) {
            sessionStorage.setItem("page_" + hash, data);
            onload(data);
        });
    }

}

/**
 * 添加路由
 * @param {string} path string 路径
 * @param {object} routeObject 路径对象
 */
function route(path, routeObject) {
    var object = routeObject || {
        depends: "",
        title: "iRead:爱阅读",
        container: "",
        onexit: function onexit() {
        },
        onrender: function () {

        },
        onenter: function onenter(query, string) {
        }
    };
    object.path = path;
    routes.push(object);
}

function frame(path, routeObject) {
    var object = routeObject || {
        depends: "",
        onexit: function onexit() {
        },
        onenter: function onenter(query, string) {
        }
    };
    frames[path] = object;
}


function loadFrame(title, url, params, configs, loading) {
    var frame = frames[url];
    if (!frame) return;


    var dependsDefer = $.Deferred();
    if (frame.depends) {


        request(frame.depends, params).done(function (result) {
            dependsDefer.resolve(result);

        }).fail(function (data) {
            dependsDefer.reject({
                code: 500,
                msg: "服务器错误"
            });

        });
    } else {
        dependsDefer.resolve(null);
    }

    var htmlDefer = $.Deferred();

    loadHtml(url, function (html) {
        htmlDefer.resolve(html);
    });
    var libsDefer = $.Deferred();

    if (frame.libs) {

        resource.use(frame.libs, function () {

            libsDefer.resolve();
        })
    } else {

        libsDefer.resolve();
    }
    var id = null;

    if (loading) {
        id = mdbAdmin.loading.show("body", loading);
    }
    $.when(dependsDefer, htmlDefer, libsDefer, resource.jsAll(frame.js), resource.cssAll(frame.css)).done(function (result, html) {
        if (id) mdbAdmin.loading.hide(id);

        var config = $.extend({}, configs, {
            title: title,
            body: html,
            oncreate: function (dom) {
                frame.onenter(params, dom, result)
            },
            onrender: function (dom, id) {
                if (typeof frame.onrender === "function") {
                    frame.onrender(params, dom, result, "#" + id);
                }
            },
            onclose: function (id) {
                if (typeof frame.onexit === "function") {
                    try {
                        frame.onexit();
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        })
        mdbAdmin.modal.show(config)

    });

}