var objOkTab = "";
layui.use(["element","layer", "okUtils", "okMock", "okTab", "okLayer", "okContextMenu",  "okToastr"], function () {
	var okUtils = layui.okUtils;
	var $ = layui.jquery;
	var layer = layui.layer;
	var okLayer = layui.okLayer;
	var okToastr = layui.okToastr;
	okToastr.options.closeButton = true;
	var okMock = layui.okMock;

	//判断登录
	okUtils.ajax(okMock.admin.login.isLogin, "post", null, true).done(function (response) {
		let data = response.data;
		layui.$(".head_name").html(data.name);
		layui.$(".version").html(data.version);
		layui.$(".head_img").attr('src',data.url);
	}).fail(function (error) {
		window.location = "./pages/login.html";
	});

	const okTab = layui.okTab({
		// 菜单请求路径
		url: okMock.admin.nav.list,
		// 允许同时选项卡的个数
		openTabNum: 30,
		// 如果返回的结果和navs.json中的数据结构一致可省略这个方法
		parseData: function (data) {
			console.log(data)
			return data;
		}
	});


	objOkTab = okTab;
	okLoading && okLoading.close();
	/**关闭加载动画*/

	$(".layui-layout-admin").removeClass("orange_theme blue_theme");
	$(".layui-layout-admin").addClass(config.theme);

	if (config.menuArrow) { //tab箭头样式
		$("#navBar").addClass(config.menuArrow);
	}

	/**
	 * 左侧导航渲染完成之后的操作
	 */
	okTab.render(function () {
		/**tab栏的鼠标右键事件**/
		$("body .ok-tab").okContextMenu({
			width: 'auto',
			itemHeight: 30,
			menu: [
				{
					text: "定位所在页",
					icon: "ok-icon ok-icon-location",
					callback: function () {
						okTab.positionTab();
					}
				},
				{
					text: "关闭当前页",
					icon: "ok-icon ok-icon-roundclose",
					callback: function () {
						okTab.tabClose(1);
					}
				},
				{
					text: "关闭其他页",
					icon: "ok-icon ok-icon-roundclose",
					callback: function () {
						okTab.tabClose(2);
					}
				},
				{
					text: "关闭所有页",
					icon: "ok-icon ok-icon-roundclose",
					callback: function () {

						okTab.tabClose(3);
					}
				}
			]
		});
	});



	/**
	 * 添加新窗口
	 */
	$("body").on("click", "#navBar .layui-nav-item a, #userInfo a", function () {
		// 如果不存在子级
		if ($(this).siblings().length == 0) {
			okTab.tabAdd($(this));
		}
		// 关闭其他展开的二级标签
		$(this).parent("li").siblings().removeClass("layui-nav-itemed");
		if (!$(this).attr("lay-id")) {
			var topLevelEle = $(this).parents("li.layui-nav-item");
			var childs = $("#navBar > li > dl.layui-nav-child").not(topLevelEle.children("dl.layui-nav-child"));
			childs.removeAttr("style");
		}
	});

	/**
	 * 左侧菜单展开动画
	 */
	$("#navBar").on("click", ".layui-nav-item a", function () {
		if (!$(this).attr("lay-id")) {
			var superEle = $(this).parent();
			var ele = $(this).next('.layui-nav-child');
			var height = ele.height();
			ele.css({"display": "block"});
			// 是否是展开状态
			if (superEle.is(".layui-nav-itemed")) {
				ele.height(0);
				ele.animate({height: height + "px"}, function () {
					ele.css({height: "auto"});
				});
			} else {
				ele.animate({height: 0}, function () {
					ele.removeAttr("style");
				});
			}
		}
	});

	/**
	 * 左边菜单显隐功能
	 */
	$(".ok-menu").click(function () {
		$(".layui-layout-admin").toggleClass("ok-left-hide");
		$(this).find("i").toggleClass("ok-menu-hide");
		localStorage.setItem("isResize", false);
		setTimeout(function () {
			localStorage.setItem("isResize", true);
		}, 1200);
	});

	/**
	 * 移动端的处理事件
	 */
	$("body").on("click", ".layui-layout-admin .ok-left a[data-url], .ok-make", function () {
		if ($(".layui-layout-admin").hasClass("ok-left-hide")) {
			$(".layui-layout-admin").removeClass("ok-left-hide");
			$(".ok-menu").find('i').removeClass("ok-menu-hide");
		}
	});

	/**
	 * tab左右移动
	 */
	$("body").on("click", ".okNavMove", function () {
		var moveId = $(this).attr("data-id");
		var that = this;
		okTab.navMove(moveId, that);
	});

	/**
	 * 刷新当前tab页
	 */
	$("body").on("click", ".ok-refresh", function () {
		okTab.refresh(this, function (okTab) {
			//刷新之后所处理的事件
		});
	});

	/**
	 * 关闭tab页
	 */
	$("body").on("click", "#tabAction a", function () {
		var num = $(this).attr("data-num");
		okTab.tabClose(num);
	});

	/**
	 * 键盘的事件监听
	 */
	$("body").on("keydown", function (event) {
		event = event || window.event || arguments.callee.caller.arguments[0];

		// 按 Esc
		if (event && event.keyCode === 27) {
			console.log("Esc");
			$("#fullScreen").children("i").eq(0).removeClass("layui-icon-screen-restore");
		}
		// 按 F11
		if (event && event.keyCode == 122) {
			console.log("F11");
			$("#fullScreen").children("i").eq(0).addClass("layui-icon-screen-restore");
		}
	});






	/**
	 * 退出操作
	 */
	$("#logout").click(function () {
		okLayer.confirm("确定要退出吗？", function (index) {
			okTab.removeTabStorage(function (res) {
				okTab.removeTabStorage();
				okUtils.ajax(okMock.admin.login.logout,"POST",null,true).done(function (response) {
					window.location = "./pages/login.html";
				}).fail(function (error) {
					window.location = "./pages/login.html";
				});

			});
		});
	});




});
