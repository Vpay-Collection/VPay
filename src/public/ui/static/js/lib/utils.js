"use strict";
layui.define(["layer","okConfig"], function (exports) {
   const $ = layui.jquery;
   const obj = {
      session: function (name, value) {
         if (value) { /**设置*/
            if (typeof value == "object") {
               sessionStorage.setItem(name, JSON.stringify(value));
            } else {
               sessionStorage.setItem(name, value);
            }
         } else if (null !== value) {
            /**获取*/
            let val = sessionStorage.getItem(name);
            try {
               val = JSON.parse(val);
               return val;
            } catch (err) {
               return val;
            }
         } else { /**清除*/
            return sessionStorage.removeItem(name);
         }
      },
      /**
       * localStorage 二次封装
       */
      local: function (name, value) {
         if (value) { /**设置*/
            if (typeof value == "object") {
               localStorage.setItem(name, JSON.stringify(value));
            } else {
               localStorage.setItem(name, value);
            }
         } else if (null !== value) {
            /**获取*/
            let val = localStorage.getItem(name);
            try {
               val = JSON.parse(val);
               return val;
            } catch (err) {
               return val;
            }
         } else { /**清除*/
            return localStorage.removeItem(name);
         }
      },

      /**
       * 获取body的总宽度
       */
      getBodyWidth: function () {
         return document.body.scrollWidth;
      },
      /**
       * 主要用于针对表格批量操作操作之前的检查
       * @param table
       * @returns {string}
       */
      tableBatchCheck: function (table) {
         const checkStatus = table.checkStatus("tableId");
         const rows = checkStatus.data.length;
         if (rows > 0) {
            let idsStr = "";
            for (let i = 0; i < checkStatus.data.length; i++) {
               idsStr += checkStatus.data[i].id + ",";
            }
            return idsStr;
         } else {
            layer.msg("未选择有效数据", {offset: "t", anim: 6});
         }
      },
      /**
       * 在表格页面操作成功后弹窗提示
       * @param content
       */
      tableSuccessMsg: function (content) {
         layer.msg(content, {icon: 1, time: 1000}, function () {
            // 刷新当前页table数据
            $(".layui-laypage-btn")[0].click();
         });
      },
      /**
       * 获取父窗体的okTab
       * @returns {string}
       */
      getOkTab: function () {
         return parent.objOkTab;
      },
      /**
       * 格式化当前日期
       * @param date
       * @param fmt
       * @returns {void | string}
       */
      dateFormat: function (date, fmt) {
         date = date || new Date();
         fmt = fmt || "yyyy年M月s日";
         const o = {
            "M+": date.getMonth() + 1,
            "d+": date.getDate(),
            "h+": date.getHours(),
            "m+": date.getMinutes(),
            "s+": date.getSeconds(),
            "q+": Math.floor((date.getMonth() + 3) / 3),
            "S": date.getMilliseconds()
         };
         if (/(y+)/.test(fmt))
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
         for (const k in o)
            if (new RegExp("(" + k + ")").test(fmt))
               fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
         return fmt;
      },
      number: {
         /**
          * 判断是否为一个正常的数字
          * @param num
          */
         isNumber: function (num) {
            return num && !isNaN(num);
         },
         /**
          * 判断一个数字是否包括在某个范围
          * @param num
          * @param begin
          * @param end
          */
         isNumberWith: function (num, begin, end) {
            if (this.isNumber(num)) {
               return num >= begin && num <= end;
            }
         },
      },

   };
   exports("utils", obj);
});
