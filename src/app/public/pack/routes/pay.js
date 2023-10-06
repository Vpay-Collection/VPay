route("pay", {
    title: "收银台",
    depends:"index/main/pay",
     heartInstance : null,
     heartInstance2 : null,
    onenter: function (query, dom,result) {
        if(result.code!==200){
            go("error")
            return true;
        }

    },
    onrender: function (query, dom,result) {
        var that = this;

        function error(msg) {
          sessionStorage.setItem("error",msg);
          go("error")
        }

        function startCountdown(start_time, timeout) {
            const currentTime = Math.floor(Date.now() / 1000);
            const remainingSeconds = timeout * 60 - (currentTime - start_time) % (timeout * 60);
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            if (remainingSeconds <= 1) {
                error("订单已超时");
            }
            const minutesString = minutes < 10 ? '0' + minutes : minutes;
            const secondsString = seconds < 10 ? '0' + seconds : seconds;
            return `${minutesString}:${secondsString}`;
        }

        function isPay() {
            /* jshint undef: false */
            $.post("/api/pay/payState", {}, function (data) {
                if (data.code === 200) {
                    switch (data.data.state) {
                        case -1:
                            error("该订单已从服务器端关闭");
                            break;
                        case 2:
                        case 3:
                            clearInterval(that.heartInstance);
                            clearInterval(that.heartInstance2);
                            location.href = data.data['return_url'];
                            break;
                    }
                } else {
                    error("该订单已被删除");
                }
            }, 'json');


        }

        that.heartInstance = setInterval(isPay, 2000);
        that.heartInstance2 = setInterval(function () {
            $("#time").text(startCountdown(result.data.start, result.data.timeout));
        }, 1000);

    },
    onexit: function () {
        clearInterval(that.heartInstance);
        clearInterval(that.heartInstance2);
    },
});
