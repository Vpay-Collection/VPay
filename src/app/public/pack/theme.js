/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
$.extend($, {
    wait: function (selector, callback, noHide, interval, maxAttempts) {
        noHide = noHide || false;

        interval = interval || 100; // 默认轮询间隔为100毫秒
        maxAttempts = maxAttempts || 1000; // 默认最大轮询次数为10次
        var attempts = 0;


        function check() {
            var item = $(selector);
            if (item.length) {
                if ((noHide && item.css("display") !== "none") || !noHide) {
                    clearInterval(timer);
                    callback.call(item); // 执行回调并将 Zepto 元素作为上下文
                }
            }
            attempts++;
            if (attempts >= maxAttempts) {
                clearInterval(timer);
            }

        }

        var timer = setInterval(check, interval);
        check();
        return this; // 返回 Zepto 元素以支持链式调用
    },
});

function resetTheme() {
    if ((window.matchMedia('(prefers-color-scheme: dark)')).matches) {
        document.querySelectorAll('[class*="-light"]').forEach(function (k) {

            k.classList.forEach(function (cls) {
                if (cls.includes('-light')) {
                    var newCls = cls.replace('-light', '-dark');
                    k.classList.replace(cls, newCls);
                }
            });
        });
    } else {
        document.querySelectorAll('[class*="-dark"]').forEach(function (k) {
            k.classList.forEach(function (cls) {
                if (cls.includes('-dark')) {
                    var newCls = cls.replace('-dark', '-light');
                    k.classList.replace(cls, newCls);
                }
            });
        });
    }
}

function onChange(e) {
    const themeLink = document.getElementById('theme-link');
    resetTheme();
    if (e.matches) {
        themeLink.href = "mdb/css/mdb.dark.min.css";
    } else {
        themeLink.href = "mdb/css/mdb.min.css";
    }
    document.cookie = "theme=" + (e.matches ? "dark" : "light") + "; path=/; max-age=" + 365 * 24 * 60 * 60;
}

window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", onChange);

onChange(window.matchMedia('(prefers-color-scheme: dark)'));
resetTheme();

function hideLoading() {
    document.getElementById('loadingOverlay').style.opacity = "0";
    setTimeout(function () {
        document.getElementById('loadingOverlay').style.display = "none";
    }, 500);
}


