<?php

use app\lib\speed\Dump;
use app\Log;
use app\Speed;


function url($c = 'main', $a = 'index', $param = array())
{
    if (is_array($c)) {
        $param = $c;
        if (isset($param['m'])) {
            $c = $param['m'] . '/' . $param['c'];
            unset($param['m'], $param['c']);
        } else {
            $c = $param['c'];
            unset($param['c']);
        }
        $a = $param['a'];
        unset($param['a']);
    }
    $params = empty($param) ? '' : '&' . http_build_query($param);
    if (strpos($c, '/') !== false) {
        list($m, $c) = explode('/', $c);
        $route = "$m/$c/$a";
        $url = $_SERVER["SCRIPT_NAME"] . "?m=$m&c=$c&a=$a$params";
    } else {
        $m = 'index';
        $route = "$c/$a";
        $url = $_SERVER["SCRIPT_NAME"] . "?c=$c&a=$a$params";
    }

    if (!empty($GLOBALS['rewrite'])) {
        if (!isset($GLOBALS['url_array_instances'][$url])) {
            foreach ($GLOBALS['rewrite'] as $rule => $mapper) {
                $mapper = '/^' . str_ireplace(array('/', '<a>', '<c>', '<m>'), array('\/', '(?P<a>\w+)', '(?P<c>\w+)', '(?P<m>\w+)'), $mapper) . '/i';
                if (preg_match($mapper, $route, $matchs)) {
                    $rule = str_ireplace(array('<a>', '<c>', '<m>'), array($a, $c, $m), $rule);
                    $match_param_count = 0;
                    $param_in_rule = substr_count($rule, '<');
                    if (!empty($param) && $param_in_rule > 0) {
                        foreach ($param as $param_key => $param_v) {
                            if (false !== stripos($rule, '<' . $param_key . '>')) $match_param_count++;
                        }
                    }
                    if ($param_in_rule == $match_param_count) {
                        $GLOBALS['url_array_instances'][$url] = $rule;
                        if (!empty($param)) {
                            $_args = array();
                            foreach ($param as $arg_key => $arg) {
                                $count = 0;
                                $GLOBALS['url_array_instances'][$url] = str_ireplace('<' . $arg_key . '>', $arg, $GLOBALS['url_array_instances'][$url], $count);
                                if (!$count) $_args[$arg_key] = $arg;
                            }
                            $GLOBALS['url_array_instances'][$url] = preg_replace('/<\w+>/', '', $GLOBALS['url_array_instances'][$url]) . (!empty($_args) ? '?' . http_build_query($_args) : '');
                        }

                        if (0 !== stripos($GLOBALS['url_array_instances'][$url], $GLOBALS['http_scheme'])) {
                            $GLOBALS['url_array_instances'][$url] = $GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') . '/' . $GLOBALS['url_array_instances'][$url];
                        }
                        return $GLOBALS['url_array_instances'][$url];
                    }
                }
            }
            return isset($GLOBALS['url_array_instances'][$url]) ? $GLOBALS['url_array_instances'][$url] : $url;
        }
        return $GLOBALS['url_array_instances'][$url];
    }
    return $url;
}

/**
 * @param null $var 需要输出的变量
 * @param bool $exit 是否退出
 * @return bool|string
 */

function dump($var, $exit = false)
{
    $line = debug_backtrace()[0]['file'] . ':' . debug_backtrace()[0]['line'];
    echo <<<EOF
<style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div style="text-align: left">
<pre class="xdebug-var-dump" dir="ltr"><small>{$line}</small>\r\n
EOF;
    $dump = new Dump();
    $dump->reconstructDump($var);
    echo '</pre></div>';
    if ($exit) exit();
    else return '';
}

/**
 * @param string $name
 * @param string $default
 * @param bool $trim 移除字符串两侧的空白字符或其他预定义字符
 * @param string $filter
 * @return mixed|string|null
 */

function arg($name = null, $default = null, $trim = false, $filter = null)
{
    switch ($filter) {
        case Speed::filter_get:
            $_REQUEST = $_GET;
            break;
        case Speed::filter_post:
            $_REQUEST = $_POST;
            break;
        case Speed::filter_cookie:
            $_REQUEST = $_COOKIE;
            break;
        default:
    }
    if (!isset($_REQUEST['m'])) $_REQUEST['m'] = 'index';
    if ($name) {
        if (!isset($_REQUEST[$name])) return $default;
        $arg = $_REQUEST[$name];
        if ($trim) $arg = trim($arg);
    } else {
        $arg = $_REQUEST;
    }
    return $arg;
}

/**
 * 日志记录
 * @param string $msg
 * @param string $type
 * @param string $name
 */
function logs($msg, $type = 'debug', $name = 'speedphp')
{

    $log = new Log(APP_LOG . date('Y-m-d') . DS . $name . '.log');
    switch ($type) {
        case 'debug':
            $log->DEBUG($msg);
            break;
        case 'info':
            $log->INFO($msg);
            break;
        case 'warn':
            $log->WARN($msg);
            break;
        default:
            $log->ERROR($msg);
            break;
    }
}

/**
 * 获得完整域名（包含协议）
 * @return string
 */
function getAddress()
{
    return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"];
}

/**
 * 获取客户端浏览器信息 添加win10 edge浏览器判断
 * @param null
 * @return string
 */
function getBroswer()
{
    $sys = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  //获取用户代理字符串
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    } elseif (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    } elseif (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    } elseif (stripos($sys, "OPR") > 0) {
        preg_match("/OPR\/([\d.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];
    } elseif (stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    } elseif (stripos($sys, "Chrome") > 0) {
        preg_match("/Chrome\/([\d.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
        preg_match("/rv:([\d.]+)/", $sys, $IE);
        $exp[0] = "IE";
        $exp[1] = $IE[1];
    } else {
        $exp[0] = "未知浏览器";
        $exp[1] = "";
    }
    return $exp[0] . '(' . $exp[1] . ')';
}

/**
 * 获取客户端操作系统信息包括win10
 * @param null
 * @return string
 */
function getOS()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $os = false;

    if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
        $os = 'Windows 95';
    } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
        $os = 'Windows ME';
    } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
        $os = 'Windows 98';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
        $os = 'Windows Vista';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
        $os = 'Windows 7';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
        $os = 'Windows 8';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
        $os = 'Windows 10';#添加win10判断
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
        $os = 'Windows XP';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
        $os = 'Windows 2000';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
        $os = 'Windows NT';
    } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
        $os = 'Windows 32';
    } else if (preg_match('/linux/i', $agent)) {
        $os = 'Linux';
    } else if (preg_match('/unix/i', $agent)) {
        $os = 'Unix';
    } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'SunOS';
    } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'IBM OS/2';
    } else if (preg_match('/Mac/i', $agent)) {
        $os = 'Mac OS X';
    } else if (preg_match('/PowerPC/i', $agent)) {
        $os = 'PowerPC';
    } else if (preg_match('/AIX/i', $agent)) {
        $os = 'AIX';
    } else if (preg_match('/HPUX/i', $agent)) {
        $os = 'HPUX';
    } else if (preg_match('/NetBSD/i', $agent)) {
        $os = 'NetBSD';
    } else if (preg_match('/BSD/i', $agent)) {
        $os = 'BSD';
    } else if (preg_match('/OSF1/i', $agent)) {
        $os = 'OSF1';
    } else if (preg_match('/IRIX/i', $agent)) {
        $os = 'IRIX';
    } else if (preg_match('/FreeBSD/i', $agent)) {
        $os = 'FreeBSD';
    } else if (preg_match('/teleport/i', $agent)) {
        $os = 'teleport';
    } else if (preg_match('/flashget/i', $agent)) {
        $os = 'flashget';
    } else if (preg_match('/webzip/i', $agent)) {
        $os = 'webzip';
    } else if (preg_match('/offline/i', $agent)) {
        $os = 'offline';
    } else {
        $os = '未知操作系统';
    }
    return $os;
}

/**
 * 获取客户端真实IP
 * @return array|false|mixed|string
 */
function getIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "127.0.0.1"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "127.0.0.1"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "127.0.0.1"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown"))
        $ip = $_SERVER["REMOTE_ADDR"];
    else
        $ip = "127.0.0.1";
    return $ip;
}

/**
 * 检查编码
 * @param $string
 * @return string
 */
function chkCode($string)
{
    $encode = mb_detect_encoding($string, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
    return mb_convert_encoding($string, 'UTF-8', $encode);
}

/**
 * 该函数执行后，后面的代码全部后台执行
 * @param $url  string 完整的URL
 * @param string $method 调用的方法
 * @param array $data  传递的数据
 * @param array $cookie cookie数组
 */
function syncRequest($url, $method = 'GET', $data = array(), $cookie = array())
{

    $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
    $port = $url_array['scheme']=='http' ? 80:443;

    $fp = fsockopen(($url_array['scheme']=='http'?"http://":'ssl://').$url_array['host'], $port, $errno, $errstr, 30);
    if (!$fp) {
        return;
    }
    if ($method == 'GET')
        $getPath = $url_array['path'] . "?" . http_build_query($data);
    else
        $getPath = $url_array['path'];

    $header = $method . " " . $getPath;
    $header .= " HTTP/1.1\r\n";
    $header .= "Host: " . $url_array['host'] . "\r\n"; //HTTP 1.1 Host域不能省略
    $token =   \includes\AES::getRandom(128);
    file_put_contents(APP_TMP.'sync_token',json_encode(array('token'=>$token,'timeout'=>time()+60*2)));
    $header .= "Token: " . $token . "\r\n"; //加入TOKEN防止恶意请求
    $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
    /**//*以下头信息域可以省略
        $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
        $header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
        $header .= "Accept-Language: en-us,en;q=0.5 ";
        $header .= "Accept-Encoding: gzip,deflate\r\n";
         */

    $header .= "Connection:Close\r\n";
    if (!empty($cookie)) {
        $_cookie = strval(NULL);
        foreach ($cookie as $k => $v) {
            $_cookie .= $k . "=" . $v . "; ";
        }
        $cookie_str = "Cookie: " . $_cookie . " \r\n";//传递Cookie
        $header .= $cookie_str;
    }

    if (!empty($data)) {
        $_post = "\r\n".http_build_query($data);
        $post_str = "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
        $post_str .= "Content-Length: " . strlen($_post) . " \r\n";//POST数据的长度
        $post_str .= $_post . "\r\n\r\n "; //传递POST数据
        $header .= $post_str;
    }

    fwrite($fp, $header);
    //echo fread($fp, 1024); //我们不关心服务器返回
    fclose($fp);
    return;
}
function background($str='',$time=0){
    ignore_user_abort(true); // 后台运行，不受前端断开连接影响
    set_time_limit($time);
//后台运行的后面还要，set_time_limit(0); 除非在服务器上关闭这个程序，否则下面的代码将永远执行下去止到完成为止。
//如果程序运行不超时,在没有执行结束前,程序不会自动结束的.
//=========================================
//PHP中，在客户端发出请求触发脚本执行，然后在服务器端执行一段代码，页面关闭了也要继续执行，并且要先返回一些状态给客户端，避免前端等待超时。

    ob_end_clean();//清除之前的缓冲内容，这是必需的，如果之前的缓存不为空的话，里面可能有http头或者其它内容，导致后面的内容不能及时的输出
    header("Connection: close");//告诉浏览器，连接关闭了，这样浏览器就不用等待服务器的响应
    header("HTTP/1.1 200 OK"); //可以发送200状态码，以这些请求是成功的，要不然可能浏览器会重试，特别是有代理的情况下

//return false;//加了这个下面的就不执行了，不加这个无法返回页面状态，浏览器一直在等待状态，可以关闭，但不是要的效果。
//die(); 或 return ;也一样不执行下面的
//runRack();自定义函数
//register_shutdown_function("runRack");
//return  ;

    ob_start();//开始当前代码缓冲
    echo $str;
//下面输出http的一些头信息
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();//输出当前缓冲
    flush();//输出PHP缓冲
//在Yii2框架下，上面代码可能不会立即返回给客户端，所以需要加如下的代码，即可实现立即返回给客户端
//具体可查看此文章：http://www.lampnick.com/php/375
    if (function_exists("fastcgi_finish_request")) {
        fastcgi_finish_request(); /* 响应完成, 关闭连接 */
    }
    /*
      休眠PHP，也就是当前PHP代码的执行停止，20秒钟后PHP被唤醒，
      PHP唤醒后，继续执行下面的代码，但这个时候上面代码的结果已经输出浏览器了，
      也就是浏览器从HTTP头中知道了服务端关闭了连接，浏览器将不在等待服务器的响应，
      反应给客户的就是页面不会显示处于加载状态，换句话说用户可以关掉当前页面，或者关掉浏览器，
      PHP唤醒后继续执行下面的代码，这也就实现了PHP后台执行的效果，
      休眠的作用只是让php先把前面的输出作完，不要急于马上执行下面的代码，休息一下而已，也就是说下面的代码
      执行的时候前面的输出应该到达浏览器了
    */
    sleep(1);

}

/**
 * 获取header
 * @return array|false
 */
function getHeader() {
    if(function_exists('getallheaders'))return getallheaders();
    $headers = array();
    foreach ($_SERVER as $key => $value) {
        if ('HTTP_' == substr($key, 0, 5)) {
            $headers[ucfirst(strtolower(str_replace('_', '-', substr($key, 5))))] = $value;
        }
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $header['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $header['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $header['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $header['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
        }
    }
    return $headers;
}

/**
 * 判断当前是否为调试状态
 * @return bool
 */
function isDebug(){
    return isset($GLOBALS['debug'])&&$GLOBALS['debug'];
}