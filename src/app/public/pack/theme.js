// 添加版权声明
/*
 * Copyright (c) 2023. Ankio. 由CleanPHP4强力驱动。
 */

// 使用 jQuery 轮询等待函数
$.extend($, {
	wait: function (selector, callback) {
		var interval = 100; // 默认轮询间隔为100毫秒
		var maxAttempts = 200; // 默认最大尝试次数为50
		var attempts = 0;

		var timer = setInterval(function () {
			var $elements = $(selector);
			if ($elements.length) {
				clearInterval(timer);
				callback.call($elements);
			}
			if (++attempts >= maxAttempts) {
				clearInterval(timer);
			}
		}, interval);
	},
	uuid: function () {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
				.toString(16)
				.substring(1);
		}

		// 添加字符前缀确保UUID不以数字开头
		return "a" + s4() + s4() + s4();
	}
});

// 主题切换函数，使用 jQuery
function switchTheme(isDark) {
	var suffix = isDark ? "-dark" : "-light";
	var style_suffix = isDark ? ".dark" : "";
	var style = "/@static/mdb/css/mdb" + style_suffix + ".min.css";
	var theme = $("#theme-link");
	if (theme.attr("href") !== style) {
		theme.attr("href", style);
	}

	$("*[class*=\"" + (isDark ? "-light" : "-dark") + "\"]").each(function () {
		var $this = $(this);
		var classList = $this.attr("class").split(/\s+/);
		$.each(classList, function (index, className) {
			if (className.indexOf(suffix) === -1) {
				$this.removeClass(className).addClass(className.replace(isDark ? "-light" : "-dark", suffix));
			}
		});
	});

}

// 监听系统主题变化并应用到页面
window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", function (e) {
	switchTheme(e.matches);
});

// 使用 jQuery 隐藏加载提示
function hideLoading() {
	$("#loadingOverlay").fadeTo(500, 0, function () {
		$(this).remove();
	});
}

function resetTheme() {
	switchTheme(window.matchMedia("(prefers-color-scheme: dark)").matches);
}

resetTheme();