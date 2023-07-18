/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

const sidenav = document.getElementById("sidenav-1");
const sidenavInstance = mdb.Sidenav.getInstance(sidenav);

let innerWidth = null;

const setMode = () => {
    // Check necessary for Android devices
    if (window.innerWidth === innerWidth) {
        return;
    }

    innerWidth = window.innerWidth;

    if (window.innerWidth < 1400) {
        sidenavInstance.changeMode("over");
        sidenavInstance.hide();
    } else {
        sidenavInstance.changeMode("side");
        sidenavInstance.show();
    }

    $('a.active').removeClass('ripple-surface');
};

setMode();
// Event listeners
window.addEventListener("resize", setMode);

/*jshint unused: false */
function dateFormat(fmt, date) {
    if (date === "" || date === 0) {
        return "";
    }
    date = date === undefined ? new Date() : new Date(parseInt(date) * 1000);
    fmt = fmt || "yyyy年M月d日";
    var o = {
        "M+": date.getMonth() + 1,
        "d+": date.getDate(),
        "h+": date.getHours(),
        "m+": date.getMinutes(),
        "s+": date.getSeconds(),
        "q+": Math.floor((date.getMonth() + 3) / 3),
        "S": date.getMilliseconds()
    };
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
    }

    for (const k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }

    }
    return fmt;
}

/*$(document).pjax('a[pjax]', '#container');
$(document).on('pjax:beforeReplace', function() {
    Object.keys(__listeners).forEach((type) => {
        __listeners[type].forEach((listener) => {
            $(listener.target).off(type);
            listener.target.removeEventListener(type, listener.listener);
        });
    });
    $(".sidenav-backdrop").remove();
});*/


var loading = {
    show(){
        const loader1 = `
  <div class="loading-full">
    <div class="spinner-border loading-icon text-succes"></div>
    <span class="loading-text">Loading...</span>
  </div>
`;
        const test2 = document.querySelector('body');
        test2.insertAdjacentHTML('beforeend', loader1);
        const loadingFull = document.querySelector('.loading-full');

        const loading = new mdb.Loading(loadingFull, {
            scroll: false,
            backdropID: 'full-backdrop'
        });
    },
    hide(){
        const backdrop = document.querySelector('#full-backdrop');
        backdrop.remove();
        document.querySelector('.loading-full').remove();
        document.body.style.overflow = '';
    }
};