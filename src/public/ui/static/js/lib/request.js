"use strict";
layui.define(["layer","okConfig","loading"], function (exports) {
   const $ = layui.jquery;
   const okConfig = layui.okConfig;
   const  loading = layui.loading;
   const obj = {
      getUrl: function (url) {
         if(url.startsWith("http"))return url;
         let baseUrl = okConfig.baseUrl;
         return baseUrl + url;
      },
      /**
       * ajax()函数二次封装
       * @param url
       * @param type
       * @param params
       * @param elem 需要渲染的elem
       * @returns {*|never|{always, promise, state, then}}
       */
      call: function (url, type, params, elem,text) {
         params = params || {};
         text = text || "正在响应请求...";
         params["token"] = localStorage.getItem("token")
         if (okConfig.isDebug)
            console.log("发送", url, params)
         const deferred = $.Deferred();
         $.ajax({
            url: this.getUrl(url),
            type: type || "get",
            data: params || {},
            dataType: "json",
            beforeSend: function () {
               loading.show(text,elem);
            },
            success: function (data) {
               if (okConfig.isDebug) {
                  console.log("返回", url, data)
               }
               if (data.code === 0 || data.code === 200) {
                  // 业务正常
                  deferred.resolve(data);
               } else {
                  console.log("okUtils.ajax warn: " + data.msg)
                  // 业务异常
                  deferred.reject(data);
               }
            },
            complete: function () {
               loading.close(elem);
            },
            error: function () {
             //  loading.close(elem);
               layer.msg("服务器错误", {icon: 2, time: 2000});
               deferred.reject("okUtils.ajax error: 服务器错误");
            }
         });
         return deferred.promise();
      },

   };
   exports("request", obj);
});
