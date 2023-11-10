route("pay", {
    title: "收银台",
    depends:"index/main/pay",
     heartInstance : null,
     heartInstance2 : null,
    onenter: function (query, dom,result) {
        if(result.code!==200){
            sessionStorage.setItem("error",result.msg);
            go("error");
            return true;
        }

        replaceTpl(dom,result.data);

    },
    onrender: function (query, dom,result) {
        var that = this;

        function error(msg) {
          sessionStorage.setItem("error",msg);
          go("error");
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
            $.post("/api/pay/payState", {order_id:query.id}, function (data) {
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
            try{
                $("#time").text(startCountdown(result.data.start, result.data.timeout));
            }catch (e) {
                clearInterval(that.heartInstance);
                clearInterval(that.heartInstance2);
            }

        }, 1000);

        function isMobileDevice() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            // Check for iOS and Android devices
            return /android/i.test(userAgent) || /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
        }
        if(isMobileDevice()){
            function extractUrlFromQueryString(fullUrl) {
                // Create a URL object
                const urlObj = new URL(fullUrl);

                // Get the 'url' query parameter
                const encodedUrl = urlObj.searchParams.get("url");

                // Decode the URL
                return decodeURIComponent(encodedUrl);
            }
            try{
                window.open("alipays://platformapi/startapp?saId=10000007&qrcode="+extractUrlFromQueryString(result.data.image));
            }catch (e) {
                
            }
        }
        //https://qr.alipay.com/bax04959iz6vrgvvt3fq30f6

    },
    onexit: function () {
        clearInterval(this.heartInstance);
        clearInterval(this.heartInstance2);
    },
});
