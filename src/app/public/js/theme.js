/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

function resetTheme() {
    if ((window.matchMedia('(prefers-color-scheme: dark)')).matches){
        document.querySelectorAll('[class*="-light"]').forEach(function (k) {

            k.classList.forEach(function(cls) {
                if (cls.includes('-light')) {
                    var newCls = cls.replace('-light', '-dark');
                    k.classList.replace(cls, newCls);
                }
            });
        });
    }else{
        document.querySelectorAll('[class*="-dark"]').forEach(function (k) {
            k.classList.forEach(function(cls) {
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
        themeLink.href = themeLink.href.replace("mdb.min.css","mdb.dark.min.css");
    } else {
        themeLink.href = themeLink.href.replace("mdb.dark.min.css","mdb.min.css");
    }
    document.cookie = "theme=" + (e.matches ? "dark" : "light") + "; path=/; max-age=" + 365 * 24 * 60 * 60;
}

window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", onChange);

onChange(window.matchMedia('(prefers-color-scheme: dark)'));
resetTheme();
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loadingOverlay').style.opacity = "0";
    setTimeout(function () {
        document.getElementById('loadingOverlay').style.display = "none";
    },500);
});


