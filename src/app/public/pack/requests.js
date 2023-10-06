function request(url, data, loading,add) {

	add = add!==undefined && add!==null && add!==false;
	url = (url.startsWith("http") || add) ? url : ("api/" + url);

	const defer = $.Deferred();

	var post = !$.isEmptyObject(data);
	var loadingId = null;
	console.log("[请求]",post ? "POST" : "GET", url, data);
	$.ajax({
		url: url,
		type: post ? "POST" : "GET",
		data: data,
		dataType: "json",
		beforeSend: function () {

			if (loading) {
				loadingId = mdbAdmin.loading.show("#app", loading)
			}
		},
		success: function (data) {

			console.log("[响应]", url, data);

			if (data.code === 401) {
				defer.reject(data);
				location.href = routeSegmentation + "login"
			}
			if(data.code===302){
				location.href = data.data;
				return;
			}
			if (data.code === 200 ) {
				defer.resolve(data);
			} else {

				if (data.msg !== "" && data.msg !== null && data.msg !== undefined) {
					sessionStorage.setItem("error",data.msg )
					mdbAdmin.toast.error(data.msg);
				}
				defer.reject(data);
			}

		},
		complete: function () {
			if (loadingId) {
				mdbAdmin.loading.hide(loadingId);
			}
		},
		error: function (e) {
			console.error("请求错误", e);
			mdbAdmin.toast.error("网络错误");
			defer.reject({"code": 500, "msg": "网络错误"});
		}
	});
	return defer.promise();
}
