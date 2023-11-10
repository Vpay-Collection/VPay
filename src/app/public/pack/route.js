(function (global, document, $) {
	"use strict";
	var routes = [];
	var frames = [];
	let lastHash = null;
	var ignoreHashChange = false;

	function handleRouting() {
		log.success(window.location.pathname, "route");
		if (window.location.pathname === lastHash) return;
		routerGo({
			oldURL: lastHash,
			newURL: window.location.pathname,
		});
		lastHash = window.location.pathname;
	}

	function getQueryParams(searchString) {
		let params = {};
		// 移除字符串开头的"?"
		let queryStr = searchString[0] === "?" ? searchString.substring(1) : searchString;
		// 分割字符串为键值对数组
		let queryArr = queryStr.split("&");
		// 遍历数组，填充params对象
		queryArr.forEach(function (pair) {
			let [key, value] = pair.split("=");
			if (key) { // 确保key非空
				params[decodeURIComponent(key)] = decodeURIComponent(value || "");
			}
		});
		return params;
	}

	/**
	 * 查询提取成数组
	 * @return {{value: string, key: string}[]|*[]}
	 */
	function getQueryAsArray() {
		var appendArguments = window.appendArguments || {};
		return Object.assign({}, appendArguments, getQueryParams(location.search));
	}

	function getHash(route) {
		return route || "";
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
		//  window.appendArguments = {};
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
					const matches = [...route.matchAll(value.path)];
					window.appendArguments = matches[0]["groups"];
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
		var hashIndex = url.indexOf("@");
		return hashIndex !== -1 ? url.slice(hashIndex + 1) : "";
	}

	function pathHtml(handler, html, data) {
		var container = handler.container || "#app";
		var $container = $(container); // 缓存jQuery对象，避免重复查询DOM

		// 确保容器存在，否则调用路由器
		if (handler.reference !== undefined && $container.length === 0) {
			routerGo({
				oldURL: null,
				newURL: "/@" + handler.reference,
			});
			// return; // 如果容器不存在，后续操作无需执行
		}

		// 使用 $.wait 自定义函数，这个函数需要检查是否有效
		$.wait(container, function () {
			var $dom = $(this); // 与 'dom' 一致但更明确是jQuery对象
			$dom.fadeOut(function () {
				if (handler.title) {
					$("title").html(lang(router.titlePrefix) + " - " + lang(handler.title));
				}

				var $htmlDom = $("<div>").html(html); // 创建新元素而不是字符串拼接
				var queryParams = getQueryAsArray();

//此处预检
				mdbAdmin.autoLoadComponents($htmlDom, function () {
					var result = handler.onenter(queryParams, $htmlDom, data);


					if (!result) {
						$dom.html($htmlDom.contents()); // 插入HTML内容
					}

					mdbAdmin.initComponents(container,function () {
						$dom.fadeIn();
						if (typeof handler.onrender === "function") {
							handler.onrender(queryParams, $htmlDom, data);
						}
						hideLoading();
					});
				});


			});
		}, true);
	}


	/**
	 * 进行路由
	 * @param event
	 */
	function routerGo(event) {
		if (router.ignoreHashChange) {
			router.ignoreHashChange = false;
			return;
		}
		var oldHash = getHashFromURL(event.oldURL);
		var newHash = getHashFromURL(event.newURL);
		var newHandler = getRouter(newHash);
		var oldHandler = getRouter(oldHash);


		if (oldHandler && typeof oldHandler.onexit === "function") {
			try {
				oldHandler.onexit();
			} catch (e) {
				console.error("退出异常", e);
			}
		}

		if (newHandler) {
			var dependsDefer = $.Deferred();

			if (newHandler.depends) {
				var depends = $.isArray(newHandler.depends) ? newHandler.depends : [newHandler.depends];
				var wait = depends.map(function (depend) {
					var defer = $.Deferred();
					request(depend, getQueryAsArray()).always(function (result) {
						defer.resolve(result);
					});
					return defer;
				});

				$.when.apply($, wait).done(function () {
					dependsDefer.resolve(arguments.length === 1 ? arguments[0] : arguments);
				});
			} else {
				dependsDefer.resolve(null);
			}

			var htmlDefer = $.Deferred();

			loadHtml(newHandler.page || newHash, function (html) {
				htmlDefer.resolve(html);
			});

			var loadingElem = null;

			if (newHandler.loading) {
				loadingElem = mdbAdmin.loading("body", newHandler.loading);
			}

			var libsDefer = $.Deferred();

			resourceLoader.use(newHandler, function () {
				libsDefer.resolve();
			});

			$.when(dependsDefer, htmlDefer, libsDefer, resourceLoader.jsAll(newHandler.js), resourceLoader.cssAll(newHandler.css)).done(function (result, html) {
				if (loadingElem) loadingElem.hide();
				pathHtml(newHandler, html, result);
			});
		} else {
			go("error");
		}
	}

	function loadHtml(newHash, onload) {

		var hash = getHash(newHash);
		var html = sessionStorage.getItem("page_" + hash);
		if (!window.debug && html) {
			onload(replaceTplLang(html));
		} else {
			if (hash === "") hash = "index";
			$.ajax({
				url: "/@static/pages/" + hash + ".html",
				type: "GET",
				headers: {"If-Modified-Since": sessionStorage.getItem("page_modify_" + hash)},
				complete: function (xhr, textStatus) {
					let data = xhr.responseText;
					if (xhr.status === 304) {
						data = sessionStorage.getItem("page_" + hash);
					} else {
						var lastModified = xhr.getResponseHeader("Last-Modified");
						sessionStorage.setItem("page_modify_" + hash, lastModified);
						sessionStorage.setItem("page_" + hash, data);
					}
					onload(replaceTplLang(data));

				},
			});

		}
	}

	global.loadFrame = function (title, url, params, configs, loading) {
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

			resourceLoader.use(frame.libs, function () {

				libsDefer.resolve();
			});
		} else {

			libsDefer.resolve();
		}
		var loadElem = null;

		if (loading) {
			loadElem = mdbAdmin.loading("body", loading);
		}
		$.when(dependsDefer, htmlDefer, libsDefer, resourceLoader.jsAll(frame.js), resourceLoader.cssAll(frame.css)).done(function (result, html) {
			if (loadElem) loadElem.hide();


			var config = $.extend({}, configs, {
				title: title,
				body: html,
				oncreate: function (dom) {
					frame.onenter(params, dom, result);
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
			});
			mdbAdmin.modal.show(config);

		});

	};

	function convertRouteToRegexPattern(route) {
		if (route.indexOf(":") === -1) {
			return route;
		}
		const variableRegex = /:([a-zA-Z0-9_]+)/g;
		const regexPattern = route.replace(variableRegex, "(?<$1>[^\/]+)");
		const escapedPattern = regexPattern.replace(/\//g, "\\/");
		return new RegExp(escapedPattern, "g");
	}

	//动态注册路由
	global.route = function (path, routeObject) {
		routeObject.path = convertRouteToRegexPattern(path);
		routes.push(routeObject);
	};
	global.frame = function (path, routeObject) {
		frames[path] = routeObject;
	};
	global.go = function (path, params) {
		var query = params ? "?" + $.param(params) : "";
		window.history.pushState({}, "", "/@" + path + query);
		handleRouting();
	};


	global.router = {
		titlePrefix: "Ankioの用户中心",
		ignoreHashChange: false,
		init: function () {
			window.addEventListener("popstate", handleRouting);
			handleRouting();
		},
		reload: function () {
			routerGo({
				oldURL: lastHash,
				newURL: window.location.pathname,
			});
		}
	};
})(window, document, jQuery);