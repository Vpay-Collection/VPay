var log = {
    info(msg, title) {
        this.pretty(title, msg, "primary");
    },
    error(msg, title) {
        this.pretty(title, msg, "danger");
    },
    success(msg, title) {
        this.pretty(title, msg, "success");
    },
    warning(msg, title) {
        this.pretty(title, msg, "warning");
    },
    typeColor(type = "default") {
        let color = "";
        switch (type) {
            case "primary":
                color = "#2d8cf0";
                break;
            case "success":
                color = "#19be6b";
                break;
            case "info":
                color = "#909399";
                break;
            case "warning":
                color = "#ff9900";
                break;
            case "danger":
                color = "#f03f14";
                break;
            default:
                color = "#35495E";
                break;
        }
        return color;
    },
    print(text, type, back) {
        back = back || false;
        if (typeof text === "object") { // 如果是对象则调用打印对象方式
            console.dir(text);
            return;
        }
        if (back) { // 如果是打印带背景图的
            console.log(
                `%c ${text} `,
                `background:${this.typeColor(type)}; padding: 2px; border-radius: 4px;color: #fff;`
            );
        } else {
            console.log(
                `%c ${text} `,
                `color: ${this.typeColor(type)};`
            );
        }
    },
    pretty(title, text, type) {
        title = title || "Mdb Pro Admin";
        if (typeof text === "object") { // 如果是对象则调用打印对象方式
            this.print(title, type, true);
            console.log(text);
            return;
        }
        console.log(
            `%c ${title} %c ${text} %c`,
            `background:${this.typeColor(type)};border:1px solid ${this.typeColor(type)}; padding: 1px; border-radius: 4px 0 0 4px; color: #fff;`,
            `border:1px solid ${this.typeColor(type)}; padding: 1px; border-radius: 0 4px 4px 0; color: ${this.typeColor(type)};`,
            "background:transparent"
        );
    }
};