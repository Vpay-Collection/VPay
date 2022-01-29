<!--****************************************************************************
  * Copyright (c) 2022. CleanPHP. All Rights Reserved.
  ***************************************************************************-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <meta name="robots" content="noindex, nofollow, noarchive"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><{$msg nofilter}></title>
    <style type="">body {
            padding: 0;
            margin: 0;
            word-wrap: break-word;
            word-break: break-all;
            font-family: Courier, Arial, sans-serif;
            background: #EBF8FF;
            color: #5E5E5E;
        }

        div, h2, p, span {
            margin: 0;
            padding: 0;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style-type: none;
            font-size: 0;
            line-height: 0;
        }

        #body {
            width: 918px;
            margin: 0 auto;
        }

        #main {
            width: 918px;
            margin: 13px auto 0 auto;
            padding: 0 0 35px 0;
        }

        #contents {
            width: 918px;
            float: left;
            margin: 13px auto 0 auto;
            background: #FFF;
            padding: 8px 0 0 9px;
        }

        #contents h2 {
            display: block;
            background: #CFF0F3;
            font: bold: 20px;
            padding: 12px 0 12px 30px;
            margin: 0 10px 22px 1px;
        }

        #contents ul {
            padding: 0 0 0 18px;
            font-size: 0;
            line-height: 0;
        }

        #contents ul li {
            display: block;
            padding: 0;
            color: #8F8F8F;
            background-color: inherit;
            font: normal 14px Arial, Helvetica, sans-serif;
            margin: 0;
        }

        #contents ul li span {
            display: block;
            color: #408BAA;
            background-color: inherit;
            font: bold 14px Arial, Helvetica, sans-serif;
            padding: 0 0 10px 0;
            margin: 0;
        }

        #oneborder {
            width: 800px;
            font: normal 14px Arial, Helvetica, sans-serif;
            border: #EBF3F5 solid 4px;
            margin: 0 30px 20px 30px;
            padding: 10px 20px;
            line-height: 23px;
        }

        #oneborder span {
            padding: 0;
            margin: 0;
        }

        #oneborder #current {
            background: #CFF0F3;
        }

        pre {

            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div id="main">
    <div id="contents">
        <pre><{$dump nofilter}></pre>
        <h2>
            <pre><{$msg nofilter}></pre>
        </h2>
        <{$index = 0}>
        <{foreach $array as $trace}>
            <ul>
                <li><span><{$trace["file"]}> on line <{$trace["line"]}> </span></li>
            </ul>
            <div id="oneborder">
                <{foreach $trace["data"] as $singleLine}>
                    <{$singleLine nofilter}>
                <{/foreach}>
            </div>
        <{/foreach}>
    </div>
</div>
<div style="clear:both;padding-bottom:50px;"></div>
</body>
</html>