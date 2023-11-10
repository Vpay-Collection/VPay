// IIFE (Immediately Invoked Function Expression) to avoid polluting global namespace
(function (global, document, $) {
	"use strict";

	var loadPlugins = [];
	var dir = "";
	var waitLoading = [];

	var loadResource = [];

	function getPath() {
		/* if (dir !== "") {
			 return dir;
		 }
		 var scripts = document.scripts;
		 var lastScript = scripts[scripts.length - 1];
		 var currentScript = document.currentScript || lastScript;
		 dir = currentScript.src.substring(0, currentScript.src.lastIndexOf("/") + 1);*/
		return "/@static/";
	}

	function hasLoaded(href) {
		return loadResource.indexOf(href) !== -1;
	}

	function load(type, href) {
		var deferred = $.Deferred();
		if (waitLoading.indexOf(href) !== -1) {
			deferred.resolve();
			return deferred.promise();
		}

		if (hasLoaded(href) || !href) {
			deferred.resolve();
		} else {
			log.info(href, "LoadingResource");
			var element;
			if (type === "css") {
				element = document.createElement("link");
				element.rel = "stylesheet";
				element.type = "text/css";
				element.href = href;
			} else if (type === "js") {
				element = document.createElement("script");
				element.type = "text/javascript";
				element.src = href;
			}

			element.onload = function (e) {
				removeWait();
				loadResource.push(href);

				if (this.src) {
					console.log(this.src);
				}

				deferred.resolve();
			};
			element.onerror = function () {
				removeWait();
				deferred.reject();
			};

			var head = document.head;
			var existingLinks = head.querySelector("#theme-link");
			document.head.insertBefore(element, existingLinks);
			waitLoading.push(href);

			function removeWait() {
				var index = waitLoading.indexOf(href);
				if (index > -1) {
					waitLoading.splice(index, 1);
				}
			}
		}

		return deferred.promise();
	}

	function loadAll(type, hrefs) {
		var deferred = $.Deferred();
		var promises = [];
		for (var i = 0; i < hrefs.length; i++) {
			promises.push(load(type, getPath() + hrefs[i]));
		}
		$.when.apply($, promises).then(function () {
			deferred.resolve();
		}, deferred.reject);
		return deferred.promise();
	}

	function loadModule(type, path, module) {
		var modulePath = path + "mdb/" + type + "/modules/" + module;
		return load(type, modulePath + (type === "css" ? ".min.css" : ".min.js"));
	}

	// Expose the resource loader to the global object
	global.resourceLoader = {
		cssAll: function (hrefs) {
			if (!hrefs) return $.Deferred().resolve().promise();
			return loadAll("css", typeof hrefs === "string" ? [hrefs] : hrefs);
		},
		jsAll: function (hrefs) {
			if (!hrefs) return $.Deferred().resolve().promise();
			return loadAll("js", typeof hrefs === "string" ? [hrefs] : hrefs);
		},
		use: function (plugins, fn) {

			if (!plugins.length) {
				fn();
				return;
			}
			if (typeof plugins === "string") {
				plugins = [plugins];
			}
			var promises = [];
			for (var i = 0; i < plugins.length; i++) {
				var path = plugins[i];
				promises.push(load("js", getPath() + "mdb/plugins/js/" + path + ".min.js"));
				promises.push(load("css", getPath() + "mdb/plugins/css/" + path + ".min.css"));
			}
			$.when.apply($, promises).done(function () {
				//初始化加载的插件
				//mdbAdmin.initComponent(loadPlugins);
				fn();
			});
		},
		getLoadPlugins() {
			return loadPlugins;
		},
		module: function (modules, fn) {

			/*	if (!modules.length) {
					fn();
					return;
				}
				if (typeof modules === "string") {
					modules = [modules];
				}
				var promises = [];
				var basePath = getPath(); // 使用已经存在的 getPath 函数获取基础路径
				for (var i = 0; i < modules.length; i++) {
					var moduleName = modules[i];
					promises.push(loadModule("js", basePath, moduleName));
					promises.push(loadModule("css", basePath, moduleName));
				}
				$.when.apply($, promises).done(function () {
					//  mdbAdmin.initComponent(loadPlugins);
					fn();
				});*/

			fn();
		}
	};

})(window, document, jQuery);
