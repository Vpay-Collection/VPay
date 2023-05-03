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
    } else {
        themeLink.href = '/clean_static/css/mdb.min.css';
        changeNavbar("navbar-dark", "navbar-light");

    }
    document.cookie = "theme=" + (e.matches ? "dark" : "light") + "; path=/; max-age=" + 365 * 24 * 60 * 60;
}


window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", onChange);
onChange(window.matchMedia('(prefers-color-scheme: dark)'));
/*
const addEventListener = Element.prototype.addEventListener;
let __listeners = {};

Element.prototype.addEventListener = function (type, listener) {
    __listeners = __listeners || {};
    if (__listeners[type]) {
        __listeners[type].push({
            target: this,
            listener,
            source: new Error().stack.split("\n")[2].trim(),
        });
    } else {
        __listeners[type] = [
            {
                target: this,
                listener,
                source: new Error().stack.split("\n")[2].trim(),
            },
        ];
    }

    addEventListener.call(this, type, listener);
};
*/
