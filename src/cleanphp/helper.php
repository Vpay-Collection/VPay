<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * File helper.php
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 12:47
 * Description : 助手函数
 */

use cleanphp\App;
use cleanphp\base\Argument;
use cleanphp\base\Dump;
use cleanphp\base\Route;
use cleanphp\closure\SerializableClosure;
use cleanphp\process\Async;
use cleanphp\process\AsyncObject;

/**
 * 获取静态文件的content-type
 * @param string $filename
 * @return string
 */
function file_type(string $filename): string
{
    $mime_types = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $ext = strtolower($extension);
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
    return 'application/octet-stream';
}

/**
 * 输出所有变量
 * @param ...$args
 * @return void
 */
function dumps(...$args)
{
    $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'] . "\n";
    foreach ($args as $v) {
        dump($v, false, $line);
    }
    App::exit("调用输出命令退出");
}

/**
 * 输出变量内容
 * @param null $var 预输出的变量名
 * @param false $exit 输出变量后是否退出进程
 * @param string|null $line
 */
function dump($var, bool $exit = true, string $line = null)
{
    if (!App::$debug) return;//不是调试模式就直接返回
    if ($line === null)
        $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'] . "\n";
    if (App::$cli) {
        echo $line;
        var_dump($var);
        if ($exit) {
            App::exit("调用输出命令退出");
        }
        return;
    }
    if ($line !== "") {
        echo <<<EOF
<style>pre {display: block;padding: 10px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left">
<pre class="xdebug-var-dump" dir="ltr"><small>{$line}</small>\r\n
EOF;
    } else {
        echo <<<EOF
<style>pre {display: block;padding: 10px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left"><pre class="xdebug-var-dump" dir="ltr">
EOF;
    }
    $dump = new Dump();
    echo $dump->dumpType($var);
    echo '</pre></div>';
    if ($exit) {
        App::exit("调用输出命令退出");
    }
}

/**
 * 传入样例类型，对目标进行类型转换
 * @param $sample mixed 样例
 * @param $data  mixed 需要转换的类型
 * @return bool|float|int|mixed|string
 */
function parse_type($sample, $data)
{
    if (is_array($data)) return $data;
    elseif (is_int($sample)) return intval($data);
    elseif (is_string($sample)) return strval($data);
    elseif (is_bool($sample)) return boolval($data);
    elseif (is_float($sample)) return floatval($data);
    elseif (is_double($sample)) return doubleval($data);
    return $data;
}

/**
 * 从get参数中获取
 * @param ?string $key
 * @param mixed $default
 * @return bool|float|int|mixed|string|null
 */
function get(string $key = null, $default = null)
{
    return Argument::get($key, $default);
}

/**
 * 从post参数中获取
 * @param ?string $key
 * @param mixed $default
 * @return bool|float|int|mixed|string|null
 */
function post(string $key = null, $default = null)
{
    return Argument::post($key, $default);
}

/**
 * 从所有参数中获取
 * @param ?string $key
 * @param mixed $default
 * @return bool|float|int|mixed|string|null
 */
function arg(string $key = null, $default = null)
{
    return Argument::arg($key, $default);
}

/**
 * 生成符合路由规则的URL
 * @param string $m 模块名
 * @param string $c 控制器名
 * @param string $a 方法
 * @param array $param 参数数组
 *
 * @return string
 */
function url(string $m = 'index', string $c = 'main', string $a = 'index', array $param = []): string
{
    return Route::url(...func_get_args());
}

/**
 * Serialize
 *
 * @param mixed $data
 * @return string
 */
function __serialize($data): string
{
    SerializableClosure::enterContext();
    SerializableClosure::wrapClosures($data);
    $data = serialize($data);
    SerializableClosure::exitContext();
    return $data;
}

/**
 * Unserialize
 *
 * @param string $data
 * @param array|null $options
 * @return mixed
 */
function __unserialize(string $data, array $options = null)
{
    if(empty($data))return null;
    try{
        SerializableClosure::enterContext();
        $data = ($options === null || PHP_MAJOR_VERSION < 7)
            ? unserialize($data)
            : unserialize($data, $options);
        SerializableClosure::unwrapClosures($data);
        SerializableClosure::exitContext();
    }catch (\cleanphp\exception\NoticeException $exception){
        return $data;
    }
    return $data;
}

/**
 * 启动一个异步任务
 * @param Closure $function 任务函数
 * @param int $timeout 异步任务的最长运行时间,单位为秒
 * @return AsyncObject
 */
function go(Closure $function, int $timeout = 300): AsyncObject
{
    return Async::start($function, $timeout);
}

/**
 * 等待任务执行完成
 * @param ...$objects
 * @return void
 */
function wait(...$objects)
{
    Async::wait(...$objects);
}

/**
 * 不区分大小写的in_array
 * @param $value
 * @param $array
 * @return bool
 */
function in_array_case($value, $array): bool
{
    if (!is_array($array)) return false;
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 *  获取随机字符串
 * @param int $length 字符串长度
 * @param bool $upper 是否包含大写字母
 * @param bool $lower 是否包含小写字母
 * @param bool $number 是否包含数字
 * @return string
 */
function rand_str(int $length = 8, bool $upper = true, bool $lower = true, bool $number = true): string
{
    $charsList = [
        'abcdefghijklmnopqrstuvwxyz',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '0123456789',
    ];
    $chars = "";
    if ($upper) {
        $chars .= $charsList[1];
    }
    if ($lower) {
        $chars .= $charsList[0];
    }
    if ($number) {
        $chars .= $charsList[2];
    }
    if ($chars === "") {
        $chars = $charsList[2];
    }
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    return $password;
}
