var heartInstance = null;
var heartInstance2 = null;

function error(msg) {
    clearInterval(heartInstance);
    clearInterval(heartInstance2);
    $("#error_msg_body").text(msg);
    $(".wait-pay").hide();
}

function startCountdown(start_time, timeout) {
    const currentTime = Math.floor(Date.now() / 1000);
    const remainingSeconds = timeout * 60 - (currentTime - start_time) % (timeout * 60);
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    console.log(remainingSeconds);
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
                    clearInterval(heartInstance);
                    clearInterval(heartInstance2);
                    location.href = data.data['return_url'];
                    break;
            }
        } else {
            error("该订单已被删除");
        }
    }, 'json');


}

heartInstance = setInterval(isPay, 2000);
heartInstance2 = setInterval(function () {
    /* jshint undef: false */
    $("#time").html(startCountdown(start, timeout));
}, 1000);