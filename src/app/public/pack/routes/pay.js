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
        let price = (result.data.price - result.data.real_price).toFixed(2);
        if(price==='0.00'){
            dom.find(".preferential").hide();
        }else{
            let showPrice = price;
            let  text = "";
            if(price>0){
                text = "优惠";
            }else{
                text = "溢价";
                showPrice = -showPrice;
            }
            dom.find(".preferential-name").text(text);
            dom.find(".preferential-price span").text(showPrice);

            mdbAdmin.modal.show({
                title: text+"提醒",
                body: `因系统负载过高，您本次订单存在<span class="text-danger">￥${showPrice}</span>的${text}，支付时请支付<span class="text-danger">￥${result.data.real_price}</span>。`,
                color: mdbAdmin.modal.color.error,
            });

        }

        if(result.data.type===3){
            dom.find(".wait-pay-title").text("微信扫码支付");
        }

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
            if(result.data.type!==2){
                return;
            }
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
