<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
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
use cleanphp\base\Error;
use cleanphp\base\EventManager;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\file\Log;
use Closure;
use Exception;

class Async
{
    private static bool $in_task = false;

    public static function register(): void
    {
        if (App::$cli) {
            return;
        }
        EventManager::addListener("__route_end__", function ($event, &$data) {
            $array = $data;
            if ($array["m"] === "async" && $array["c"] === "task" && $array["a"] === "start") {
                Variables::set("__frame_log_tag__", "async_");
                Async::response();
            } else {
                ignore_user_abort(false);
                if (connection_aborted()) {
                    App::exit("客户端断开，脚本中断。");
                }
            }
        });
    }

    public static function start(Closure $function, int $timeout = 300): ?AsyncObject
    {
        if (App::$cli) {
            return null;
        }
        $key = uniqid("async_");

        $asyncObject = new AsyncObject();
        $asyncObject->timeout = $timeout;
        $asyncObject->state = AsyncObject::WAIT;
        $asyncObject->function = $function;
        $asyncObject->key = $key;

        $url = url("async", "task", "start");
        $url_array = parse_url($url);
        $query = [];
        if (isset($url_array["query"])) {
            parse_str($url_array["query"], $query);
        }
        $port = intval($_SERVER["SERVER_PORT"]);
        $scheme = Response::getHttpScheme();
        if ($query !== []) {
            $get_path = $url_array['path'] . "?" . http_build_query($query);
        } else {
            $get_path = $url_array['path'];
        }
        Cache::init($timeout, Variables::getCachePath("async", DS))->set($key, $asyncObject);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $scheme . $url_array['host'] . ":" . $port . $get_path);
            curl_setopt($ch, CURLOPT_PORT, $port);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RESOLVE, [$url_array['host'] . ':' . "127.0.0.1"]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Host: ' . $url_array['host'],
                'Key: '.$key,
                'User-Agent: Async/1.0.0.1',
                'Connection: Close'
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $exception) {
            Error::err('异步任务处理失败，可能超出服务器处理上限: ' . $exception->getMessage(), [], "Async");
            return null;
        }

        if (App::$debug) {
            Log::record("Async", "异步任务已下发：$key");
        }

        return $asyncObject;
    }

    public static function response(): void
    {
        self::noWait();
        self::$in_task = true;
        $key = Request::getHeaderValue("Key") ?? "";
        /** @var AsyncObject $asyncObject */
        $cache =  Cache::init(300, Variables::getCachePath("async", DS));
        $asyncObject = $cache->get($key);
        $cache->del($key);
        if (empty($asyncObject) || !isset($_SERVER["REMOTE_ADDR"])) {
            Log::record("Async", "找不到对应的任务！");
            App::exit("您无权访问该资源。");
            return;
        }

        $function = $asyncObject->function;
        $timeout = $asyncObject->timeout ?? 60;

        set_time_limit($timeout);
        Variables::set("__async_task_id__", $key);
        Variables::set("__frame_log_tag__", "async_{$key}_");
        App::$debug && Log::record("Async", "异步任务开始执行：" . serialize($asyncObject));
        if (!empty($function) && $function instanceof Closure) {
            $function();
        }

        App::exit("异步任务执行完毕");
    }

    public static function noWait(int $time = 0, string $outText = "")
    {
        if (App::$cli) {
            return null;
        }
        ignore_user_abort(true);
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
        ob_end_flush();
        flush();
        if (function_exists("fastcgi_finish_request")) {
            fastcgi_finish_request();
        }
    }

    public static function wait(AsyncObject ...$keys)
    {
        $array = $keys;
        while (!empty($array)) {
            foreach ($array as &$key) {
                if (empty(Cache::init($key->timeout, Variables::getCachePath("async", DS))->get($key->key))) {
                    unset($key);
                }
            }
            usleep(20000);
        }
    }
}