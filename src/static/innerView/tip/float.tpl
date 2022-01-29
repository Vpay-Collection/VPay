<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="referrer" content="no-referrer" />
    <title></title>
    <style>
        * {
            padding: 0;
            margin: 0;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        html, body {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: #fff;
        }
        html {
            font-family:'Noto Sans SC Sliced', PingFangSC-Light, Microsoft YaHei UI, Microsoft YaHei, helvetica, sans-serif;
            font-weight: 500;
            color: #000;
        }
        form, input, button {
            padding: 0;
            margin: 0;
            border: none;
            outline: none;
            background: none;
        }
        input::-webkit-input-placeholder {
            color: #ccc;
            letter-spacing: 2px;
            font-size: 16px;
        }
        ul, li {
            display: block;
            list-style: none;
        }
        a {
            text-decoration: none;
            color: #ececec;
        }

        .home a {
            font-size: 20px;
            color: #999;
            line-height: 50px;
            display: block;
            text-align: center;
        }
        #menu {
            width: 50px;
            height: 50px;
            transform: scale(0.8);
            position: absolute;
            right: 10px;
            top: 5px;
            z-index: 2000000;
            cursor: pointer;
            transition: 0.5s;
        }
        #menu i {
            position: absolute;
            left: 0;
            right: 0;
            margin: 24px auto;
            width: 30px;
            height: 2px;
            background: #777;
        }
        #menu i:before {
            content:'';
            width: 20px;
            height: 2px;
            top: -8px;
            background: #777;
            position: absolute;
            right: 0;
        }
        #menu i:after {
            content:'';
            width: 20px;
            height: 2px;
            bottom: -8px;
            background: #777;
            position: absolute;
            left: 0;
        }

        #menu.on {
            right: 800px;
            background: #29f;
            border-radius: 25px;
            box-shadow: 0 6px 8px rgba(36, 159, 253, .3);
        }
        #menu.on i {
            width: 20px;
            background: #fff;
        }
        #menu.on i:before {
            top: -5px;
            transform: rotate(45deg);
            width: 14px;
            right: -1px;
            left: auto;
            background: #fff;
        }
        #menu.on i:after {
            bottom: -5px;
            transform: rotate(-45deg);
            width: 14px;
            right: -1px;
            left: auto;
            background: #fff;
        }
        .list {
            width: 750px;
            padding: 0 20px;
            height: 100%;
            overflow: hidden;
            overflow-y: auto;
            position: absolute;
            right: 0px;
            z-index: 2000;
            background: #222d46;
            transition: 0.3s all linear;
        }
        .list.closed {
            right: -880px;
        }
        .list ul {
            width: 730px;
            float: left;
            padding: 0 0 20px;
            margin-bottom: 20px;
        }
        .list ul li {
            float: left;
            margin: 5px;
            width: 100px;
            height: 30px;
            text-align: left;
            line-height: 30px;
        }
        .list ul li a {
            width: 100%;
            border-radius: 5px;
            transition: 0.2s all linear;
            height: 100%;
            display: block;
            color: #fff;
            font-weight: 500;
            background: #293550;
            text-align: left;
            font-size: 12px;
        }

        /*.list ul li:hover a i {
            color: #fff !important;
        }
        */
        .list ul li.title {
            width: 100%;
            height: 40px;
            line-height: 40px;
            margin: 30px 0 0;
            text-align: left;
            text-indent: 10px;
            /*border-bottom: 1px dashed #dedede;*/
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }

        .list ul li.title .icon {
            width: 20px;
            height: 20px;
        }
        @media (max-width: 640px) {
            #menu.on {
                right: 270px;
            }
            .list {
                width: 227px;
            }
            .list ul {
                width: 220px;
            }
            .con .sou ul li {
                width: 100px;
                font-size: 12px;
                text-indent: 30px;
            }
        }


        .list .ullist li {
            float: left;
            margin: 5px;
            width: 100%;
            height: 30px;
            text-align: left;
            line-height: 30px;
        }
        .list .ullist li a {
            font-size: 18px;
        }
        .ullist .icon {
            width: 20px;
            height: 20px;
            margin: 0 5px 0 8px;
            vertical-align: -0.15em;
            fill: currentColor;
            overflow: hidden;
        }
        .scroll::-webkit-scrollbar {
            /*滚动条整体样式*/
            width: 5px;
            /*高宽分别对应横竖滚动条的尺寸*/
            height: 1px;
        }
        .scroll::-webkit-scrollbar-thumb {
            /*滚动条里面小方块*/
            border-radius: 5px;
            -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
            background: #535353;
        }
        .scroll::-webkit-scrollbar-track {
            /*滚动条里面轨道*/
            -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            background: #EDEDED;
        }
        .list ul li.title {
            width: 100%;
            height: 40px;
            line-height: 40px;
            margin: 30px 0 0;
            text-align: left;
            text-indent: 10px;
            /* border-bottom: 1px dashed #dedede; */
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }
        details>div{
            margin-left:30px;
            color: white;
            font-size: 14px;
            word-break: break-all;
        }
    </style>
</head>

<body>
<div id="menu" class="menu"><i></i></div>
<div class="list closed scroll" id="close">
    <div class="list_in">
        <ul class="list_ul">
            <li class="title">
                <details open>
                    <summary style=""><b>时间信息</b></summary>
                    <div id="timeInfo">
                        <div>时间：<{$time['time']}>ms</div>
                        <div>响应耗时：<{$time['resp_time']}>ms</div>
                        <div>路由耗时：<{$time['route_time']}>ms</div>
                        <div>模板编译耗时：：<{$time['tpl_time']}>ms</div>
                    </div>
                </details>
                <details>
                    <summary style=""><b>响应信息</b></summary>
                    <div id="baseInfo">

                        <div>请求：<{$response['method']}> </div>
                        <div>Headers：</div>
                        <div><{dump($response['headers'],false,true)}></div>
                        <div>参数信息：</div>
                        <div><{dump($response['param'],false,true)}></div>
                        <div>全局变量信息：</div>
                        <div><{dump($response['globals'],false,true)}></div>

                    </div>
                </details>
                <details>
                    <summary style=""><b>文件加载</b></summary>
                    <div id="fileLoad">
                        <div>总加载文件数：<{sizeof($files)}></div>
                        <div><{dump($files,false,true)}></div>
                    </div>
                </details>
                <details>
                    <summary style=""><b>路由信息</b></summary>
                    <div id="fileLoad">
                        <div><{dump($route,false,true)}></div>
                    </div>
                </details>
                <details>
                    <summary style=""><b>数据库信息</b></summary>
                    <div id="fileLoad">
                        <div><{dump($sql,false,true)}></div>
                    </div>
                </details>
                <details>
                    <summary style=""><b>程序流程</b></summary>
                    <div id="process">
                        <div><{dump($clean,false,true)}></div>
                    </div>
                </details>

            </li>
        </ul>
    </div>
</div>

</body>
<script>
    const menu = document.getElementById("menu");
    menu.addEventListener("click", function(){
        let cls = menu.getAttribute("class")
        if (cls.indexOf("on")!==-1){
            cls = cls.replace("on","");
        }else{
            cls = cls.concat(" on");
        }
        menu.setAttribute("class",cls );
        let close =  document.getElementById("close");

         cls = close.getAttribute("class")

        if (cls.indexOf("closed")!==-1){
            cls = cls.replace("closed","");
        }else{
            cls = cls.concat(" closed");
        }
        close.setAttribute("class",cls );
    });

</script>
<script>

</script>
</html>
