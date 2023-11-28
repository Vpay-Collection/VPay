class Lang {
    constructor() {
        this.langData = {};
        this.langMap = {zh: "zh-cn"};
    }

    detect(callback) {
        let that = this;
        let langSet = 'zh-cn';
        var lang = document.cookie.replace(/(?:(?:^|.*;\s*)lang\s*\=\s*([^;]*).*$)|^.*$/, "$1");

        if (lang) {
            langSet = lang;
            window.debug && log.warning(`通过Cookie获取语言：${langSet}`, "language");
        } else if (navigator.language) {
            langSet = navigator.language.toLowerCase();
            window.debug && log.warning(`通过浏览器语言获取语言：${langSet}`, "language");
            document.cookie = "lang=" + langSet + "; expires=Thu, 18 Dec 9999 12:00:00 UTC; path=/";
        }
        if (this.langMap[langSet]) {
            langSet = this.langMap[langSet];
        }

        if (!this.langData[langSet]) {
            resourceLoader.jsAll("lang/" + langSet + ".js").done(function () {
                window.debug && log.warning('语言加载成功', 'language');
                callback();
            });
        } else {
            callback();
        }
        this.lang = langSet;
    }


    add(data) {
        this.langData = data;
    }

    setLang(lang) {
        document.cookie = "lang=" + lang + "; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
        location.reload();
    }

    get(name, ...vars) {
        if (!name) {
            return "";
        }

        if (!this.langData[name]) {
            this.langData[name] = name;
            this.lang !== "zh-cn" && this.export();
            return this.format(name, ...vars);
        }

        return this.format(this.langData[name], ...vars);
    }

    // 格式化函数来处理变量替换
    format(str, ...args) {
        var index = 0;
        return str.replace(/%s/g, function () {
            return index < args.length ? args[index++] : "%s";
        });
    }

    // 提供一个方法输出当前语言数据状态
    export() {
        window.debug && console.log(`Language.add(${JSON.stringify(this.langData, null, 2)});`);
    }
}

window.Language = new Lang();

function lang(name, ...vars) {
    return Language.get(name, ...vars);
}

function replaceTplLang(tpl) {
    var regex = /lang\(([^)]+)\)/g;
    return tpl.replace(regex, function (match, group1) {
        return Language.get(group1); // 这里返回替换后的字符串
    });

}


