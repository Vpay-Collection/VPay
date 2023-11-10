(function (global, document, $) {
    'use strict';
    global.log = {
        colors: {
            primary: "#2d8cf0",
            success: "#19be6b",
            info: "#909399",
            warning: "#ff9900",
            danger: "#f03f14",
            default: "#35495E"
        },
        print: function (text, type, back) {
            type = type || "default";
            back = !!back;
            var color = this.colors[type] || this.colors.default;
            if (typeof text === "object") {
                console.dir(text);
                return;
            }
            var style = back
                ? "background:" + color + "; padding: 2px; border-radius: 4px; color: #fff;"
                : "color: " + color + ";";
            console.log("%c " + text + " ", style);
        },
        pretty: function (title, text, type) {
            if(! window.debug ) return;
            title = title || "MdbAdmin";
            type = type || "default";
            var color = this.colors[type] || this.colors.default;
            if (typeof text !== "string") {
                this.print(title, type, true);
                console.log(text);
                return;
            }
            console.log(
                "%c " + title + " %c " + text + " %c",
                "background:" + color + ";border:1px solid " + color + "; padding: 1px; border-radius: 4px 0 0 4px; color: #fff;",
                "border:1px solid " + color + "; padding: 1px; border-radius: 0 4px 4px 0; color: " + color + ";",
                "background:transparent"
            );
        }
    };

// Dynamically create the log methods for different log levels
    var logTypes = ["info", "danger", "success", "warning","primary"];
    for (var i = 0; i < logTypes.length; i++) {
        (function (type) {
            global.log[type] = function (msg, title) {
                this.pretty(title, msg, type);
            };
        })(logTypes[i]);
    }

})(window, document, jQuery);


