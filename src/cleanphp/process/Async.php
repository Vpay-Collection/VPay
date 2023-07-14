<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Class Async
 * Created By ankio.
 * Date : 2022/11/18
 * Time : 18:03
 * Description :
 */

namespace cleanphp\process;

use cleanphp\App;
use cleanphp\base\Dump;
use cleanphp\base\Error;
use cleanphp\base\EventManager;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\exception\ExitApp;
use cleanphp\exception\NoticeException;
use cleanphp\file\Log;
use Closure;


class Async
{
    private static bool $in_task = false;

    public static function register()
    {
        if (App::$cli) return;
        EventManager::addListener("__route_end__", function ($event, &$data) {
            $array = $data;
            if ($array["m"] === "async" && $array["c"] === "task" && $array["a"] === "start") {
                Variables::set("__frame_log_tag__", "async_");
                Async::response();
            } else {
                ignore_user_abort(false);
                if (connection_aborted()) {
                    //如果连接已断开
                    App::exit("客户端断开，脚本中断。");
                }
            }
        });

    }

    /**
     * 启动一个异步任务
     * @param Closure $function 任务函数
     * @param int $timeout 异步任务的最长运行时间,单位为秒
     * @return ?AsyncObject
     */
    static function start(Closure $function, int $timeout = 300): ?AsyncObject
    {
        if (App::$cli) return null;
        $key = uniqid("async_");

        Log::record("Async", "异步任务启动：$key");
        $asyncObject = new AsyncObject();
        $asyncObject->timeout = $timeout;
        $asyncObject->state = AsyncObject::WAIT;
        $asyncObject->function = $function;
        $asyncObject->key = $key;

        $url = url("async", "task", "start");
        $url_array = parse_url($url);
        $query = [];
        if (isset($url_array["query"]))
            parse_str($url_array["query"], $query);
        $port = intval($_SERVER["SERVER_PORT"]);
        $scheme = Response::getHttpScheme() === "https://" ? "ssl://" : "";
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        $context = stream_context_create($contextOptions);

        $fp = stream_socket_client($scheme . $url_array['host'] . ":" . $port, $errno, $err_str, 5, STREAM_CLIENT_CONNECT, $context);
        if ($fp === false) {
            Error::err('异步任务处理失败，可能超出服务器处理上限: ' . $err_str, [], "Async");
            return null;
        }

        if ($query !== [])
            $get_path = $url_array['path'] . "?" . http_build_query($query);
        else
            $get_path = $url_array['path'];

        $header = "GET " . $get_path;
        $header .= " HTTP/1.1" . PHP_EOL;
        $header .= "Host: " . $url_array['host'] . PHP_EOL;
        $token = md5($key);
        $asyncObject->token = $token;
        Cache::init($timeout, Variables::getCachePath("async", DS))->set($token, $asyncObject);
        $header .= "Token: " . $token . PHP_EOL;
        $header .= "User-Agent: Async/1.0.0.1 " . PHP_EOL;
        $header .= "Connection: Close" . PHP_EOL;
        $header .= PHP_EOL;
        fwrite($fp, $header);
        //此处延时关闭，防止代码未走到noWait就中断
        usleep(5 * 1000);
        fclose($fp);
        if (App::$debug) {
            Log::record("Async", "异步任务已下发：$key");
            Log::record("Async", "异步请求包：\n$header");
        }
        return $asyncObject;
    }

    /**
     *  响应后台异步请求
     * @return void
     * @throws ExitApp
     */
    public static function response()
    {
        try {
            self::noWait();
        } catch (NoticeException $exception) {

        }
        self::$in_task = true;
        $token = Request::getHeaderValue("Token") ?? "";

        /**@var $asyncObject AsyncObject* */
        $asyncObject = Cache::init(300, Variables::getCachePath("async", DS))->get($token);
        Cache::init(300, Variables::getCachePath("async", DS))->del($token);
        if (empty($asyncObject)) {
            Log::record("Async", "key检查失败！");
            App::exit("您无权访问该资源。");
            return;
        }elseif (!$asyncObject instanceof AsyncObject){
            Log::record("Async", "key检查失败！".serialize($asyncObject));
            App::exit("您无权访问该资源。");
            return;
        }

        try {
        $key = $asyncObject->key;
        $function = $asyncObject->function;
        $timeout = $asyncObject->timeout??60;
        } catch (NoticeException $exception) {
            Log::record("Async", "序列化对象错误！");

            Log::record("Async", "错误对象：".(new Dump())->dumpTypeAsString($asyncObject));

            App::exit("序列化对象错误");
             }
        set_time_limit($timeout);
        Variables::set("__async_task_id__", $key);
        Variables::set("__frame_log_tag__", "async_{$key}_");
        App::$debug && Log::record("Async", "异步任务开始执行：".__serialize($asyncObject));
        if(!empty($function) && $function instanceof Closure){
            $function();
        }

        App::exit("异步任务执行完毕");
    }

    /**
     * 后台运行
     * @param int $time 超时时间
     * @param string $outText
     * @return void
     */
    public static function noWait(int $time = 0, string $outText = "")
    {
        if (App::$cli) return null;
        ignore_user_abort(true); // 后台运行，不受前端断开连接影响
        set_time_limit($time);
        ob_end_clean();
        ob_start();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        if ($outText !== "") {
            echo $outText;
        }
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();//输出当前缓冲
        flush();
        if (function_exists("fastcgi_finish_request")) {
            fastcgi_finish_request(); /* 响应完成, 关闭连接 */
        }
    }


    /**
     * 判断当前执行环境是否为异步任务
     * @return bool
     */
    public static function isInTask(): bool
    {
        return self::$in_task;
    }

    /**
     * 等待所有任务执行完毕
     * @param ...$keys AsyncObject
     * @return void
     */
    public static function wait(...$keys)
    {
        $array = $keys;
        while (!empty($array)) {
            foreach ($array as &$key) {
                if (empty(Cache::init($key->timeout, Variables::getCachePath("async", DS))->get($key->token))) {
                    unset($key);
                }
            }
            usleep(20000);
        }
    }


}