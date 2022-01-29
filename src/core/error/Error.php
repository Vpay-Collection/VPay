<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\error;
use app\core\debug\Log;
use app\core\mvc\Controller;

use app\core\web\Response;

/**
 * Class Error
 * @package app\core\debug
 * Date: 2020/11/20 12:09 上午
 * Author: ankio
 * Description:框架错误处理
 */
class Error
{

	/**
	* 注册错误处理机制
	 */
	public static function register()
	{
		error_reporting(E_ALL);
		$data = set_error_handler([__CLASS__, 'appError']);
		set_exception_handler([__CLASS__, 'appException']);
		register_shutdown_function([__CLASS__, 'appShutdown']);
	}


    /**
     * App异常退出
     * @param string $e
     */
	public static function appException(string $e)
	{
        $traces = [];
		$err = explode('Stack trace:', $e);
		if (sizeof($err) !== 2) {
			self::err($e);
		} else {
			$msg       = $err[0];
			$isMatched = preg_match_all('/in\s(.*php):([0-9]+)/', $msg,
				$matches);
			if ($isMatched) {
				$trace["file"] = $matches[1][0];
				$trace["line"] = $matches[2][0];
				$traces[]      = $trace;
			}
			$isMatched = preg_match_all('/#[0-9]+\s(.*php)\((.*?)\):/', $err[1],
				$matches);
			if ($isMatched) {
				for ($i = 0; $i < $isMatched; $i++) {
					$trace["file"] = $matches[1][$i];
					$trace["line"] = $matches[2][$i];
					$traces[]      = $trace;
				}
				self::err($msg, $traces);
			} else {
				self::err($msg);
			}
		}
	}


    /**
     * 报错退出
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
	public static function appError(
        int    $errno,
        string $errstr,
        string $errfile = '',
        int    $errline = 0
	): bool
    {
		if (0 === error_reporting() || 30711 === error_reporting()) {
			return true;
		}
		$msg = "ERROR";
		if ($errno == E_WARNING) {
			$msg = "WARNING";
		}
		if ($errno == E_NOTICE) {
			$msg = "NOTICE";
		}
		if ($errno == E_STRICT) {
			$msg = "STRICT";
		}
		if ($errno == 8192) {
			$msg = "DEPRECATED";
		}
		self::err("$msg: $errstr in $errfile on line $errline");
        return false;
	}


	/**
	 * 无法恢复的异常
	 */
	public static function appShutdown()
	{
		if (error_get_last()) {
			$err = error_get_last();
			self::err("Fatal error: {$err['message']} in {$err['file']} on line {$err['line']}");
		}
	}

    /**
     * 高亮代码
     * @param  string $code
     * @return bool|string|string[]
     */
    private static function highlightCode(string $code)
    {
        $code = preg_replace('/(\/\*\*)/', '///**', $code);
        $code = preg_replace('/(\s\*)[^\/]/', '//*', $code);
        $code = preg_replace('/(\*\/)/', '//*/', $code);
        if (preg_match('/<\?(php)?[^[:graph:]]/i', $code)) {
            $return = highlight_string($code, true);
        } else {
            $return = preg_replace('/(&lt;\?php&nbsp;)+/i', "",
                highlight_string("<?php ".$code, true));
        }
        return str_replace(['//*/', '///**', '//*'], ['*/', '/**', '*'], $return);
    }

    /**
     * @param string $file 错误文件名
     * @param int $line 错误文件行,若为-1则指定msg查找
     * @param string $msg 当line为-1才有效
     * @return array
     */
    public static function errorFile(string $file,int $line=-1,string $msg=""): array
    {
        if (!(file_exists($file) && is_file($file))) {
            return [];
        }
        $data  = file($file);
        $count = count($data) - 1;
        $returns = [];
        if($line==-1){
            //查找文本
            for ($i = 0; $i <= $count; $i++) {
                if(strpos($data[$i],$msg)!== false){
                    $line = $i+1;
                    break;
                }
            }
        }
        $returns["line"] = $line;
        $start = $line - 5;
        if ($start < 1) {
            $start = 1;
        }
        $end = $line + 5;
        if ($end > $count) {
            $end = $count + 1;
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $line) {
                $returns[] = "<div id='current'>".$i.".&nbsp;".self::highlightCode($data[$i - 1])."</div>";
            } else {
                $returns[] = $i.".&nbsp;".self::highlightCode($data[$i- 1]);
            }
        }
        return $returns;
    }

    /**
     * 报错退出
     * @param string $msg
     * @param array $errInfo
     */
    public static function err(string $msg, array $errInfo = [])
    {
        Log::error("runError", $msg);
        $traces = sizeof($errInfo) === 0 ? debug_backtrace() : $errInfo;
        if ($dump = ob_get_contents()) {
            ob_end_clean();
        }

        if($dump!=""){
            $dump = "异常输出：".$dump;
        }
        if (!isDebug()) {
            global $__module, $__controller, $__action;
            $nameBase = "app\\controller\\$__module\\BaseController";
            if (method_exists($nameBase, 'err500')) {
                $nameBase::err500($__module, $__controller, $__action, $msg);
            } else {
                Response::msg(true, 500, 'System Error', 'Something bad.', 3,
                    '/', '立即跳转');
            }
        } else {
            global $__module;
            $__module = '';
            self::display($msg, $traces,$dump);
        }
        exit(500);
    }

    public static function display($msg, $traces,$dump)
    {
        if (isConsole()) {
            echo $dump."\n";
            echo $msg."\n";
            foreach ($traces as $trace) {
                if (is_array($trace) && ! empty($trace["file"])) {
                    echo "{$trace["file"]} on line {$trace["line"]}"."\n";
                }
            }
            return;
        }

        $index = 0;$setArray = [];
        foreach ($traces as $trace) {
            if(++$index==1&&sizeof($traces)!=1)continue;
            if (is_array($trace) && ! empty($trace["file"])) {
                $trace["keyword"] =$trace["keyword"]??"";
                $sourceLine = self::errorFile($trace["file"], $trace["line"],$trace["keyword"]);
                $trace["line"] = $sourceLine["line"];
                unset( $sourceLine["line"]);
                if ($sourceLine) {
                    $setArray[] = [
                        "file"=>$trace["file"],
                        "line" => $trace["line"],
                        "data"=>$sourceLine
                    ];
                }
            }
        }

        $obj = new Controller();
        $obj->setArray(["msg"=>$msg,"dump"=>$dump,"array"=>$setArray]);
        $obj->setAutoPathDir(APP_INNER . DS . "tip");
        echo $obj->display("error");
        exitApp($msg);
    }


}

