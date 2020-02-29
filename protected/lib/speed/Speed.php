<?php

namespace lib\speed;

use controller;

/**
 * Class Speed
 * @package lib\speed
 */
class Speed
{
    const filter_post=3;
    const filter_get=1;
    const filter_cookie=2;
    static public function Start()
    {
        GLOBAL $__module, $__controller, $__action;
        //框架开始类
        Speed::Init();
        Speed::rewrite();
        Speed::createObj();

    }

    static public function Init()
    {
        if ($GLOBALS['debug']) {
            error_reporting(-1);
            ini_set("display_errors", "On");
        } else {
            error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
            ini_set("display_errors", "Off");
        }
        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == "https") || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
            $GLOBALS['http_scheme'] = 'https://';
        } else {
            $GLOBALS['http_scheme'] = 'http://';
        }
    }

    static public function rewrite()
    {
        if (!empty($GLOBALS['rewrite'])) {
            foreach ($GLOBALS['rewrite'] as $rule => $mapper) {
                if ('/' == $rule) $rule = '/$';
                if (0 !== stripos($rule, $GLOBALS['http_scheme']))
                    $rule = $GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') . '/' . $rule;
                $rule = '/' . str_ireplace(array('\\\\', $GLOBALS['http_scheme'], '/', '<', '>', '.'),
                        array('', '', '\/', '(?P<', '>[-\w]+)', '\.'), $rule) . '/i';

                if (preg_match($rule, $GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $matchs)) {
                    $route = explode("/", $mapper);
                    if (isset($route[2])) {
                        list($_GET['m'], $_GET['c'], $_GET['a']) = $route;
                    } else {
                        list($_GET['c'], $_GET['a']) = $route;
                    }
                    foreach ($matchs as $matchkey => $matchval) {
                        if (!is_int($matchkey)) $_GET[$matchkey] = $matchval;
                    }
                    break;
                }
            }
        }
        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        GLOBAL $__module, $__controller, $__action;
        $__module = isset($_REQUEST['m']) ? strtolower($_REQUEST['m']) : 'index';
        $__controller = isset($_REQUEST['c']) ? strtolower($_REQUEST['c']) : 'main';
        $__action = isset($_REQUEST['a']) ? strtolower($_REQUEST['a']) : 'index';


    }

    static public function createObj()
    {
        GLOBAL $__module, $__controller, $__action;
        if($__controller==='base')Error::_err_router("Err: Controller 'BaseController' is not correct!Not allowed to be accessed！");
        $controller_name = ucfirst($__controller) . 'Controller';
        $action_name = 'action' . $__action;

        if (!empty($__module)) {
            if (!Speed::is_available_classname($__module)) Error::_err_router("Err: Module '$__module' is not correct!");
            if (!is_dir(APP_DIR . DS . 'protected' . DS . 'controller' . DS . $__module)) Error::_err_router("Err: Module '$__module' is not exists!");
        }
        $controller_name = 'controller\\' . $__module . '\\' . $controller_name;

        if (!self::is_available_classname($__controller)) Error::_err_router("Err: Controller '$controller_name' is not correct!");
        if (!class_exists($controller_name, true)) Error::_err_router("Err: Controller '$controller_name' is not exists!");
        if (!method_exists($controller_name, $action_name)) Error::_err_router("Err: Method '$action_name' of '$controller_name' is not exists!");

        /**
         * @var $controller_obj mvc\Controller
         */
        $controller_obj = new $controller_name();
        $controller_obj->$action_name();

        if ($controller_obj->_auto_display) {
            $auto_tpl_name = $__controller . '_' . $__action;
            if (file_exists(APP_VIEW . (empty($__module) ? '' : $__module . DS) . $auto_tpl_name . '.html')) $controller_obj->display($auto_tpl_name);
        }
    }

    /**
     * @param $name
     * @return false|int
     */
    static public function is_available_classname($name)
    {
        return preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $name);
    }

    static public function url($c = 'main', $a = 'index', $param = array())
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
    static public function dump($var, $exit = false)
    {
        $line=debug_backtrace()[0]['file'].':'.debug_backtrace()[0]['line'];
        if (!$GLOBALS['debug']){
            self::Log(print_r($var, true),'debug');
            if ($exit) exit();
            else return '';
        }
        echo <<<EOF
<style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style><div align=left>
<pre class="xdebug-var-dump" dir="ltr"><small>{$line}</small>\r\n
EOF;
        $dump=new Dump();
        $dump->reconstructDump($var);
        echo '</pre></div>';
        if ($exit) exit();
        else return '';
    }

    /**
     * @param null $name
     * @param null $default
     * @param bool $trim
     * @param null $filter
     * @return mixed|string|null
     */
    static public function arg($name = null, $default = null, $trim = false,$filter=null)
    {
        switch($filter){
            case self::filter_get:$_REQUEST2 = $_GET;break;
            case self::filter_post:$_REQUEST2 = $_POST;break;
            case self::filter_cookie:$_REQUEST2 = $_COOKIE;break;
            default:$_REQUEST2=$_REQUEST;
        }

        if (!isset($_REQUEST2['m'])) $_REQUEST2['m'] = 'index';
        if ($name) {
            if (!isset($_REQUEST2[$name])) return $default;
            $arg = $_REQUEST2[$name];
            if ($trim) $arg = trim($arg);
        } else {
            $arg = $_REQUEST2;
        }
        return $arg;
    }

    static public function Log($msg, $type = 'debug')
    {
        $log = new Log(APP_LOG . date('Y-m-d') . '.log');
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

}
