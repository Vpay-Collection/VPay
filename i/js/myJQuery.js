var $, $s;
$ = $s = function(selector, context) {
    return new $s.fn.init(selector, context);
};
$s.fn = $s.prototype;
$s.fn.init = function(selector, context) {
    var nodeList = [];
    if(typeof(selector) == 'string') {
        nodeList = (context || document).querySelectorAll(selector);
    } else if(selector instanceof Node) {
        nodeList[0] = selector;
    } else if(selector instanceof NodeList || selector instanceof Array) {
        nodeList = selector;
    }
    this.length = nodeList.length;
    for(var i = 0; i < this.length; i += 1) {
        this[i] = nodeList[i];
    }
    return this;
};
$s.fn.init.prototype = $s.fn;
$s.fn.each = function(cb_fun, need_ret) {
    var res = [];
    for(var i = 0; i < this.length; i++) {
        res[i] = cb_fun.call(this[i]);
    }
    if(need_ret) {
        if(res.length === 1) {
            res = res[0];
        }
        return res;
    }
    return this;
};
$s.fn.eq = function() {
    var nodeList = [];
    for(var i = 0; i < arguments.length; i++) {
        nodeList[i] = this[arguments[i]];
    }
    return $s(nodeList);
};
$s.fn.first = function() {
    return this.eq(0);
};
$s.fn.last = function() {
    return this.eq(this.length-1);
};
$s.fn.find = function(str) {
    var nodeList = [];
    var res=this.each(function(){
        return this.querySelectorAll(str);
    },1);
    if(res instanceof Array){
        for(var i=0;i<res.length;i++){
            for(var j=0;j<res[i].length;j++){
                nodeList.push(res[i][j]);
            }
        }
    }else{
        nodeList=res;
    }
    return $s(nodeList);
};
$s.fn.parent = function() {
    return $s(this.each(function() {
        return this.parentNode;
    }, 1));
};
$s.fn.hide = function() {
    return this.each(function() {
        this.style.display = "none";
    });
};
$s.fn.show = function() {
    return this.each(function() {
        this.style.display = "";
    });
};
$s.fn.text = function(str) {
    if(str!==undefined) {
        return this.each(function() {
            this.innerText = str;
        });
    } else {
        return this.each(function() {
            return this.innerText;
        }, 1);
    }
};
$s.fn.html = function(str) {
    if(str!==undefined) {
        return this.each(function() {
            this.innerHTML = str;
        });
    } else {
        return this.each(function() {
            return this.innerHTML;
        }, 1);
    }
};
$s.fn.outHtml = function(str) {
    if(str!==undefined) {
        return this.each(function() {
            this.outerHTML = str;
        });
    } else {
        return this.each(function() {
            return this.outerHTML;
        }, 1);
    }
};
$s.fn.val = function(str) {
    if(str!=undefined) {
        return this.each(function() {
            this.value = str;
        });
    } else {
        return this.each(function() {
            return this.value;
        }, 1);
    }
};
$s.fn.css = function(key,value,important) {
    if(value!==undefined){
        return this.each(function() {
            this.style.setProperty(key, value,important);
        });
    }else{
        return this.each(function() {
            return this.style.getPropertyValue(key);
        }, 1);
    }
};
$s.fn.attr = function(key,value) {
    if(value!==undefined) {
        return this.each(function() {
            this.setAttribute(key, value);
        });
    }else{
        return this.each(function() {
            return this.getAttribute(key);
        }, 1);
    }
};
$s.fn.removeAttr = function(key) {
    return this.each(function() {
        this.removeAttribute(key);
    });
};
$s.fn.remove = function() {
    return this.each(function() {
        this.remove();
    });
};
$s.fn.append = function(str) {
    return this.each(function() {
        this.insertAdjacentHTML('beforeend', str);
    });
};
$s.fn.prepend = function(str) {
    return this.each(function() {
        this.insertAdjacentHTML('afterbegin', str);
    });
};
$s.fn.hasClass = function(str) {
    return this.each(function() {
        return this.classList.contains(str);
    }, 1);
};
$s.fn.addClass = function(str) {
    return this.each(function() {
        return this.classList.add(str);
    });
};
$s.fn.removeClass = function(str) {
    return this.each(function() {
        return this.classList.remove(str);
    });
};
$s.fn.click = function(f){//click改为监听事件，
    if (typeof (f) == "function") {//重载，若含有参数就注册事件，无参数就触发事件
        this.each(function() {
            this.addEventListener("click", f);
        });
    } else {
        this.each(function() {
            var event = document.createEvent('HTMLEvents');
            event.initEvent("click", true, true);
            this.dispatchEvent(event);
        });
    }
};
$s.fn.tag = function(tag) {
    var dom = document.createElement(tag);
    this[0] = dom;
    return this;
};
$s.fn.dom = function(str) {
    var dom = document.createElement('p');
    dom.innerHTML = str;
    this[0] = dom.childNodes[0];
    return this;
};




$s.ajax ={
    get: function(url, fn) {
        // XMLHttpRequest对象用于在后台与服务器交换数据
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            // readyState == 4说明请求已完成
            if (xhr.readyState === 4 && xhr.status === 200 || xhr.status === 304) {
                // 从服务器获得数据
                fn.call(this, xhr.responseText);
            }
        };
        xhr.send();
    },
    // datat应为'a=a1&b=b1'这种字符串格式，在jq里如果data为对象会自动将对象转成这种字符串格式
    post: function (url, data, fn) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url, true);
        // 添加http头，发送信息至服务器时内容编码类型
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && (xhr.status === 200 || xhr.status === 304)) {
                fn.call(this, xhr.responseText);
            }
        };
        xhr.send(data);
    }
};

$s.get_url_pra=function(paraName) {
    var url = document.location.toString();
    var arrObj = url.split("?");
    if (arrObj.length > 1) {
        var arrPara = arrObj[1].split("&");
        var arr;
        for (var i = 0; i < arrPara.length; i++) {
            arr = arrPara[i].split("=");
            if (arr !== null && arr[0] === paraName) {
                return arr[1];
            }
        }
        return "";
    }
    else {
        return "";
    }
};//取得url的参数
