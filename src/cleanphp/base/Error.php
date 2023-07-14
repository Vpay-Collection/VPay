<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Error
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 23:07
 * Description :
 */

namespace cleanphp\base;

use cleanphp\App;
use cleanphp\engine\EngineManager;
use cleanphp\exception\DeprecatedException;
use cleanphp\exception\ErrorException;
use cleanphp\exception\ExitApp;
use cleanphp\exception\NoticeException;
use cleanphp\exception\StrictException;
use cleanphp\exception\WarningException;
use cleanphp\file\Log;
use Exception;
use Throwable;

class Error
{

    public static function register(): void
    {
        $old_error_handler = set_error_handler([__CLASS__, 'appError'], E_ALL);
        set_exception_handler([__CLASS__, 'appException']);
    }


    /**
     *
     * App异常退出
     * @param $e Throwable
     */
    public static function appException(Throwable $e): void
    {

        if ($e instanceof ExitApp) {
            App::$debug && Log::record("Frame", sprintf("框架执行退出: %s", $e->getMessage()));
            return;//Exit异常不进行处理
        }

       self::err("Exception: ".get_class($e)."\r\n\r\n".$e->getMessage(), array_merge([["file" => $e->getFile(), "line" => $e->getLine(), "function" => "", "class" => '', "type" => ""]], $e->getTrace()), get_class($e));
    }

    /**
     * 报错退出
     * @param string $msg 错误信息
     * @param array $errInfo 堆栈
     * @param string $log_tag 记录日志的tag
     */
    public static function err(string $msg, array $errInfo = [], string $log_tag = "ErrorInfo"): void
    {

        if (Variables::get('__frame_error__', false)) return;
        //捕获异常后清除数据
        error_clear_last();
        //避免递归调用
        Variables::set('__frame_error__', true);
        Log::record($log_tag, $msg, Log::TYPE_ERROR);
        $traces = sizeof($errInfo) === 0 ? debug_backtrace() : $errInfo;
        $trace_text = [];
        foreach ($traces as $i => &$call) {
            $trace_text[$i] = sprintf("#%s %s(%s): %s%s%s", $i, $call['file'] ?? "", $call['line'] ?? "", $call["class"] ?? "", $call["type"] ?? "", $call['function'] ?? "");
            Log::record($log_tag, $trace_text[$i], Log::TYPE_ERROR);
        }


        if ($dump = ob_get_contents()) {
            ob_end_clean();
        }

        if(App::$cli){
            var_dump($msg,$trace_text);
        }else{
            $result = self::renderError();


            $engine = EngineManager::getEngine();

            if ($result !== null) {
                (new Response())->render($result, 200, $engine->getContentType())->send();
            } else if (App::$debug) {
                (new Response())->render($engine->renderError($msg, $traces, $dump, $log_tag), 200, $engine->getContentType())->send();
            } else {
                (new Response())->render($engine->renderMsg(true, 404, "404 Not Found", "您访问的资源不存在。", 5), 404, $engine->getContentType())->send();
            }
        }


    }

    /**
     * 调用用户自定义的错误渲染器
     * @return string|null
     */
    static function renderError(): ?string
    {
        $__module = Variables::get("__request_module__", '');
        $__controller = Variables::get("__request_controller__", '');
        $controller = 'app\\' . Variables::getSite("\\") . 'controller\\' . $__module . '\\' . ucfirst($__controller);
        $base = 'app\\' . Variables::getSite("\\") . 'controller\\' . $__module . '\\BaseController';
        $method = "system_error";

        try {
            if (class_exists($controller)) {
                if (method_exists($controller, $method)) {
                    return (new $controller)->$method();
                }
            }
            if (class_exists($base)) {
                new $base;
            }
        } catch (Exception $exception) {
            Log::record('Controller', '控制器初始化函数存在严重的错误', Log::TYPE_ERROR);
        }
        return EngineManager::getEngine()->onControllerError($__controller, $method);
    }


    /**
     * 报错退出
     * @param int $errno
     * @param string $err_str
     * @param string $err_file
     * @param int $err_line
     * @return bool
     * @throws WarningException
     * @throws ErrorException
     * @throws DeprecatedException
     * @throws StrictException
     * @throws NoticeException
     */
    public static function appError(int $errno, string $err_str, string $err_file = '', int $err_line = 0): bool
    {
        if ($errno == E_WARNING) {
            throw new WarningException("WARNING: $err_str in $err_file on line $err_line");
        } elseif ($errno == E_NOTICE) {
            throw new NoticeException("NOTICE: $err_str in $err_file on line $err_line");
        } elseif ($errno == E_STRICT) {
            throw new StrictException("STRICT: $err_str in $err_file on line $err_line");
        } elseif ($errno == 8192) {
            throw new DeprecatedException("DEPRECATED: $err_str in $err_file on line $err_line");
        } else throw new ErrorException("ERROR: $err_str in $err_file on line $err_line");
    }

}
