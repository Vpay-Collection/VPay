<?php

namespace app;

use Exception;
use app\lib\speed\mvc\Controller;
use Throwable;

class Error
{
    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * 异常处理
     * @access public
     * @param Exception|Throwable $e 异常
     * @return void
     */
    public static function appException($e)
    {
        $err=explode('Stack trace:',$e);
        if(sizeof($err)!==2){
            self::err($e);
        }else{
            $msg=$err[0];
            $isMatched = preg_match_all('/in\s(.*php):([0-9]+)/', $msg, $matches);
            if($isMatched){
                $trace["file"]=$matches[1][0];
                $trace["line"]=$matches[2][0];
                $traces[]=$trace;
            }
            $isMatched = preg_match_all('/#[0-9]+\s(.*php)\((.*?)\):/', $err[1], $matches);
            if($isMatched){
                for($i=0;$i<$isMatched;$i++){
                    $trace["file"]=$matches[1][$i];
                    $trace["line"]=$matches[2][$i];
                    $traces[]=$trace;
                }
                self::err($msg,$traces);
            }else self::err($msg);
        }

    }

    /**
     * 直接报错函数
     * @param $msg
     * @param array $errinfo
     */
    public static function err($msg, $errinfo = array())
    {

        $msg = htmlspecialchars($msg);
        $traces = sizeof($errinfo) === 0 ? debug_backtrace() : $errinfo;
        if (!empty($GLOBALS['err_handler'])) {
            call_user_func($GLOBALS['err_handler'], $msg, $traces);
        } else {
            if (!$GLOBALS['debug']) {
                logs($msg, 'warn');
                $obj = new Controller();
                GLOBAL $__module;
                $__module = '';
                $obj->display($GLOBALS['error']);
                exit;
            } else {
                if (ob_get_contents()) {
                    if (!$GLOBALS['debug']) ob_end_clean();
                    logs($msg, 'warn');
                }
            }
            self::display($msg, $traces);
            exit;
        }
    }

    /**
     * 代码高亮
     * @param $code
     * @return string|string[]
     */
    public static function _err_highlight_code($code)
    {
        $code = preg_replace('/(\/\*\*)/', '///**',$code);
        $code =  preg_replace('/(\s\*)[^\/]/', '//*',$code);
        $code =  preg_replace('/(\*\/)/', '//*/',$code);
        if (preg_match('/<\?(php)?[^[:graph:]]/i', $code)) {
            $return = highlight_string($code, TRUE);
        } else {
            $return =  preg_replace('/(&lt;\?php&nbsp;)+/i', "", highlight_string("<?php " . $code, true));
        }
        return str_replace(array('//*/','///**','//*'),array('*/','/**','*'),$return);
    }

    /**
     *
     * @param $file
     * @param $line
     * @return array|mixed
     */
    public static function _err_getsource($file, $line)
    {
        if (!(file_exists($file) && is_file($file))) {
            return $GLOBALS['error'];
        }
        $data = file($file);
        $count = count($data) - 1;
        $start = $line - 5;
        if ($start < 1) {
            $start = 1;
        }
        $end = $line + 5;
        if ($end > $count) {
            $end = $count + 1;
        }
        $returns = array();
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $line) {
                $returns[] = "<div id='current'>" . $i . ".&nbsp;" . self::_err_highlight_code($data[$i - 1]) . "</div>";
            } else {
                $returns[] = $i . ".&nbsp;" . self::_err_highlight_code($data[$i - 1]);
            }
        }
        return $returns;
    }

    /**
     * 错误渲染
     * @param $msg
     * @param $traces
     */
    public static function display($msg, $traces)
    {


        echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <meta name="robots" content="noindex, nofollow, noarchive"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{$msg}</title>
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
        }</style>
</head>
<body>
<div id="main">
    <div id="contents"><h2>{$msg}</h2>
EOF;

        foreach ($traces as $trace) {
            if (is_array($trace) && !empty($trace["file"])) {
                $souceline = self::_err_getsource($trace["file"], $trace["line"]);
                if ($souceline) {
                    echo <<<EOF
                <ul><li><span>{$trace["file"]} on line {$trace["line"]} </span></li></ul>
                <div id="oneborder">
EOF;
                    foreach ($souceline as $singleline) echo $singleline;
                    echo '</div>';
                }
            }
        }

        echo <<<EOF
        </div>
</div>
<div style="clear:both;padding-bottom:50px;"></div>
</body>
</html>
EOF;

    }

    /**
     * 错误处理
     * @access public
     * @param integer $errno 错误编号
     * @param integer $errstr 详细错误信息
     * @param string $errfile 出错的文件
     * @param integer $errline 出错行号
     * @return void
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        if (0 === error_reporting() || 30711 === error_reporting()) return;
        $msg = "ERROR";
        if ($errno == E_WARNING) $msg = "WARNING";
        if ($errno == E_NOTICE) $msg = "NOTICE";
        if ($errno == E_STRICT) $msg = "STRICT";
        if ($errno == 8192) $msg = "DEPRECATED";
        self::err("$msg: $errstr in $errfile on line $errline");

    }

    /**
     * 异常中止处理
     * @access public
     * @return void
     */
    public static function appShutdown()
    {
        if (error_get_last()) {
            $err = error_get_last();
            self::err("Fatal error: {$err['message']} in {$err['file']} on line {$err['line']}");

        }
    }

    /**
     * 错误路由
     * @param $msg
     */
    public static function _err_router($msg)
    {
        Global $__module, $__controller, $__action;
        $name = "app\\controller\\$__module\\BaseController";
        if (!method_exists($name, 'err404')) {
            self::err($msg);
        } else {

            $name::err404($__module, $__controller, $__action, $msg);
        }
    }

}

