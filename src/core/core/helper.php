<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

use app\core\debug\Debug;
use app\core\debug\Dump;
use app\core\mvc\Controller;
use app\core\web\Request;
use app\core\web\Route;

/*数据库常量*/
const SQL_INSERT_NORMAL = 0;
const SQL_INSERT_IGNORE = 1;
const SQL_INSERT_DUPLICATE = 2;


/**
 * 生成符合路由规则的URL
 * @param string $m      模块名
 * @param string $c      控制器名
 * @param string $a      方法
 * @param array $param  参数数组
 *
 * @return string
 */
function url(string $m = 'index', string $c = 'main', string $a = 'index', array $param = []): string
{
	return Route::url(...func_get_args());
}


/**
 * 输出变量内容
 * @param  null   $var   预输出的变量名
 * @param false $exit  输出变量后是否退出进程
 */
function dump($var, bool $exit = false,bool $noTitle=false)
{
    if($noTitle){
        $line = "";
    }else{
        $line = debug_backtrace()[0]['file'].':'.debug_backtrace()[0]['line']."\n";
    }

    if (isConsole()) {
        echo $line;
		var_dump($var);
		if ($exit) {
            exitApp("Dump函数执行退出.");
        }

		return;
	}
    if(!$noTitle){
        echo <<<EOF
<style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left">
<pre class="xdebug-var-dump" dir="ltr"><small>{$line}</small>\r\n
EOF;
    }else{
        echo <<<EOF
<style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left"><pre class="xdebug-var-dump" dir="ltr">
EOF;
    }


	$dump = new Dump();
	$dump->dumpType($var);
	echo '</pre></div>';
	if ($exit) {
        exitApp("Dump函数执行退出.");
	}
}


/**
 * 获取前端传来的POST或GET参数
 * @param  null  $name     参数名
 * @param  null  $default  默认参数值
 * @param bool $trim     是否去除空白
 * @param string $type     类型(str,bool,float,double,int),当返回所有数据时该校验无效。
 * @return mixed
 */
function arg($name = null, $default = null, bool $trim = true, string $type="str")
{
	if ($name) {
		if ( ! isset($_REQUEST[$name])) {
			return $default;
		}
		$arg = $_REQUEST[$name];
		if ($trim) {
			$arg = trim($arg);
		}
	} else {
		$arg = $_REQUEST;
	}


	if(!is_array($arg)){
        switch ($type){
            case "str":$arg=strval($arg);break;
            case "int":$arg=intval($arg);break;
            case "bool":$arg=boolval($arg);break;
            case "float":$arg=floatval($arg);break;
            case "double":$arg=doubleval($arg);break;
            default:break;
        }
    }

	return $arg;
}



/**
 * 是否为调试模式
 * @return bool
 */
function isDebug(): bool
{
	return isset($GLOBALS["frame"]['debug']) && $GLOBALS["frame"]['debug'];
}


/**
 * 是否为命令行模式
 * @return bool
 */
function isConsole(): bool
{
	return isset($_SERVER['CLEAN_CONSOLE']) && $_SERVER['CLEAN_CONSOLE'];
}

/**
 * 退出框架运行
 * @param string $msg
 * @param string|null $tpl 退出模板文件名
 * @param string $path 模板文件路径
 * @param array $data 模板文件所需变量
 */
function exitApp(string $msg, string $tpl=null, string $path='', array $data=[])
{
    if($tpl!==null){
        $obj = new Controller();
        $obj->setArray($data);
        $obj->setAutoPathDir($path);
       if (file_exists($path.DS . $tpl . '.tpl'))
          echo  $obj->display($tpl);
        else
          echo "";
    }
    if(isDebug()){
        $GLOBALS["frame"]["clean"][] = $msg;
        $GLOBALS["frame"]["time"]["resp_time"]=(microtime(true)-$GLOBALS['frame_start']);

        $result["frame"]["time"]["执行时间"] = (microtime(true) - $GLOBALS['frame_start']) . "ms";
        $result["frame"]["time"]["模板编译时间"] = $GLOBALS["frame"]["time"]["tpl_time"] . "ms";
        $result["frame"]["time"]["路由时间"] = $GLOBALS["frame"]["time"]["route_time"] . "ms";
        $result["frame"]["frame"]["response"]["method"] = $_SERVER['REQUEST_METHOD'];
        $result["frame"]["frame"]["response"]["headers"] = Request::getHeader();
        $result["frame"]["框架日志"] = $GLOBALS["frame"]["clean"];
        $result["frame"]["路由"] = $GLOBALS["frame"]["route"];
        $result["frame"]["sql"] = $GLOBALS["frame"]["sql"];
        $result["frame"]["文件加载"] = $GLOBALS["frame"]["file"];
        $g = $GLOBALS;
        unset($g["frame"]);
        $result["frame"]["time"]["response"]["全局变量"] = $g;
        $result["frame"]["time"]["response"]["参数信息"] = arg();

        $data = print_r($result,true);
        Debug::i("frame",$data);

    }
    exit();
}


