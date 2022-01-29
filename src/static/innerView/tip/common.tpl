<!--****************************************************************************
  * Copyright (c) 2022. CleanPHP. All Rights Reserved.
  ***************************************************************************-->

<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <style >* {
        padding: 0;
        margin: 0;
    }

    div {
        padding: 4px 48px;
    }

    a {
        color: #2E5CD5;
        cursor: pointer;
        text-decoration: none
    }

    a:hover {
        text-decoration: underline;
    }

    body {
        background: #fff;
        color: #333;
        font-size: 18px;
    }

    h1 {
        font-size: 100px;
        font-weight: normal;
        margin-bottom: 12px;
    }

    p {
        line-height: 1.6em;
        font-size: 42px
    }</style>
    <title><{$title}></title></head>
<body>
<div style="padding: 24px 48px;"><h1><{$err}></h1>
    <p><span style="font-size:32px;"><{$title}></span></p>
    <p><span style="font-size:25px;"><{$msg}></span></p>
    <span id="jump_box" style="font-size:25px;">

    </span>
</div>
<script>
    let wait = "<{$time}>";
    if (parseInt(wait) !== -1) {
        document.getElementById('jump_box').innerHTML = "还有<span id='jump'><{$time}></span>秒为您自动跳转，<a href='<{$url}>' target='_self'><{$desc}></a>"
        setInterval(function () {
            document.getElementById("jump").innerText = (--wait).toString();
            if (wait <= 0) {
                location.href = "<{$url}>";
            }
        }, 1000);
    } else if ("<{$url}>" !== "") {
        document.getElementById('jump_box').innerHTML = "<span id='jump'><a href='<{$url}>' target='_self'><{$desc}></a></span>"
    }
</script>
</body>
</html>
