function changeNavbar(from, to) {
    var fromElem = document.querySelector("." + from);
    if (fromElem != null) {
        fromElem.classList.add(to);
        fromElem.classList.remove(from);
    }
    var fromColor = from === "navbar-dark" ? "dark" : "white";
    var toColor = to === "navbar-dark" ? "dark" : "white";
    var fromColorElem = document.querySelectorAll(".bg-" + fromColor);
    if (fromColorElem != null) {
        for (const fromColorElemElement of fromColorElem) {
            fromColorElemElement.classList.add("bg-" + toColor);
            fromColorElemElement.classList.remove("bg-" + fromColor);
        }

    }

}

function onChange(e) {
    const themeLink = document.getElementById('theme-link');
    if (e.matches) {
        themeLink.href = '/clean_static/css/mdb.dark.min.css';
        changeNavbar("navbar-light", "navbar-dark");
        document.querySelectorAll(".bg-light").forEach(function (value, key, parent) {
            value.classList.add("bg-dark");
            value.classList.remove("bg-light");
        });
    } else {
        themeLink.href = '/clean_static/css/mdb.min.css';
        changeNavbar("navbar-dark", "navbar-light");
        document.querySelectorAll(".bg-dark").forEach(function (value, key, parent) {
            value.classList.add("bg-light");
            value.classList.remove("bg-dark");
        });
    }
    document.cookie = "theme=" + (e.matches ? "dark" : "light") + "; path=/; max-age=" + 365 * 24 * 60 * 60;
}


window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", onChange);
onChange(window.matchMedia('(prefers-color-scheme: dark)'));
document.addEventListener('DOMContentLoaded', function() {
    hideLoading();
});

function showLoading() {
    document.getElementById('loadingOverlay').style.display =  'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display =  'none';
}
