$.ajaxSetup({
	cache: true
});
class Requests {
	constructor(url, data, loading) {
		log.primary(url,"请求");
		log.warning(data,"请求");

		const defer = $.Deferred();
		var post = data && !(typeof data === "object" && $.isEmptyObject(data));

		var loadingElem = null;
		// 显示加载提示，如果需要的话
		if (loading) {
			loadingElem = mdbAdmin.loading("body", loading);
		}

		// 发起AJAX请求
		$.ajax({
			url: "/"+url,
			type: post ? "POST" : "GET",
			data: data,
			dataType: "json",
			beforeSend: function () {
				// 如果resourceLoader已经处理，这里不需要再次处理
			},
			success: function (data) {
				log.primary(url,"响应");
				log.warning(data,"响应");
				if (data.code === 401) {
					go("login");
					defer.reject(data);
				} else if (data.code === 302) {
					location.href = data.data;
				} else if (data.code === 200) {
					defer.resolve(data);
				} else {

					if (data.msg) {
						mdbAdmin.toast.error(data.msg);
					}
					defer.reject(data);
				}
			},
			error: function (e) {

				log.danger(url,"响应");
				log.danger(e,"响应错误");

				mdbAdmin.toast.error(lang("网络错误"));
				defer.reject({"code": 500, "msg": lang("网络错误")});
			},
			complete: function () {
				if (loadingElem) {
					loadingElem.hide();
				}
			}
		});

		return defer.promise();
	}
}

function request(url, data, loading) {
	// 统一资源URL
	return new Requests(url,data,loading);
}