<?php

namespace Speed;
namespace lib\speed;
use controller;

/**
 * Class Speed
 * @package lib\speed
 */
class Speed
{

    static public function Start()
    {
        GLOBAL $__module,$__controller,$__action;
        //框架开始类
        Speed::Init();
        Speed::rewrite();
        Speed::createObj();

    }
    static  public function Init(){
        if($GLOBALS['debug']){
            error_reporting(-1);
            ini_set("display_errors", "On");
        }else{
            error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
            ini_set("display_errors", "Off");
            ini_set("log_errors", "On");
        }
        if((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == "https") || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)){
            $GLOBALS['http_scheme'] = 'https://';
        }else{
            $GLOBALS['http_scheme'] = 'http://';
        }

    }

    static public function rewrite(){
        if(!empty($GLOBALS['rewrite'])){
            foreach($GLOBALS['rewrite'] as $rule => $mapper){
                if('/' == $rule)$rule = '/$';
                if(0!==stripos($rule, $GLOBALS['http_scheme']))
                    $rule = $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') .'/'.$rule;
                $rule = '/'.str_ireplace(array('\\\\', $GLOBALS['http_scheme'], '/', '<', '>',  '.'),
                        array('', '', '\/', '(?P<', '>[-\w]+)', '\.'), $rule).'/i';
                if(preg_match($rule, $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $matchs)){
                    $route = explode("/", $mapper);

                    if(isset($route[2])){
                        list($_GET['m'], $_GET['c'], $_GET['a']) = $route;
                    }else{
                        list($_GET['c'], $_GET['a']) = $route;
                    }
                    foreach($matchs as $matchkey => $matchval){
                        if(!is_int($matchkey))$_GET[$matchkey] = $matchval;
                    }
                    break;
                }
            }
        }
        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
        GLOBAL $__module,$__controller,$__action;
        $__module     = isset($_REQUEST['m']) ? strtolower($_REQUEST['m']) : 'index';
        $__controller = isset($_REQUEST['c']) ? strtolower($_REQUEST['c']) : 'main';
        $__action     = isset($_REQUEST['a']) ? strtolower($_REQUEST['a']) : 'index';

    }
    static public function createObj(){
        GLOBAL $__module,$__controller,$__action;
        $controller_name = ucfirst($__controller).'Controller';
        $action_name = 'action'.$__action;

        if(!empty($__module)){
            if(!Speed::is_available_classname($__module))Error::_err_router("Err: Module '$__module' is not correct!");
            if(!is_dir(APP_DIR.DS.'protected'.DS.'controller'.DS.$__module))Error::_err_router("Err: Module '$__module' is not exists!");
        }
        $controller_name='controller\\'.$__module.'\\'.$controller_name;

        //var_dump($__module,$__controller,$__action);

        if(!self::is_available_classname($__controller))Error::_err_router("Err: Controller '$controller_name' is not correct!");
       if(!class_exists($controller_name, true))Error::_err_router("Err: Controller '$controller_name' is not exists!");
       if(!method_exists($controller_name, $action_name))Error::_err_router("Err: Method '$action_name' of '$controller_name' is not exists!");
        /**
         * @var $controller_obj mvc\Controller
         */

        //var_dump($controller_name,$action_name);
        $controller_obj = new $controller_name();


        $controller_obj->$action_name();

        if($controller_obj->_auto_display){
            $auto_tpl_name = $__controller.'_'.$__action;
            if(file_exists(APP_VIEW.(empty($__module) ? '' : $__module.DS).$auto_tpl_name.'.html'))$controller_obj->display($auto_tpl_name);
        }
    }

    static public function url($c = 'main', $a = 'index', $param = array()){
        if(is_array($c)){
            $param = $c;
            if(isset($param['m'])) {
                $c = $param['m'] . '/' . $param['c'];
                unset($param['m'], $param['c']);
            } else {
                $c = $param['c']; unset($param['c']);
            }
            $a = $param['a']; unset($param['a']);
        }
        $params = empty($param) ? '' : '&'.http_build_query($param);
        if(strpos($c, '/') !== false){
            list($m, $c) = explode('/', $c);
            $route = "$m/$c/$a";
            $url = $_SERVER["SCRIPT_NAME"]."?m=$m&c=$c&a=$a$params";
        }else{
            $m = 'index';
            $route = "$c/$a";
            $url = $_SERVER["SCRIPT_NAME"]."?c=$c&a=$a$params";
        }

        if(!empty($GLOBALS['rewrite'])){
            if(!isset($GLOBALS['url_array_instances'][$url])){
                foreach($GLOBALS['rewrite'] as $rule => $mapper){
                    $mapper = '/^'.str_ireplace(array('/', '<a>', '<c>', '<m>'), array('\/', '(?P<a>\w+)', '(?P<c>\w+)', '(?P<m>\w+)'), $mapper).'/i';
                    if(preg_match($mapper, $route, $matchs)){
                        $rule = str_ireplace(array('<a>', '<c>', '<m>'), array($a, $c, $m), $rule);
                        $match_param_count = 0;
                        $param_in_rule = substr_count($rule, '<');
                        if(!empty($param) && $param_in_rule > 0){
                            foreach($param as $param_key => $param_v){
                                if(false !== stripos($rule, '<'.$param_key.'>'))$match_param_count++;
                            }
                        }
                        if($param_in_rule == $match_param_count){
                            $GLOBALS['url_array_instances'][$url] = $rule;
                            if(!empty($param)){
                                $_args = array();
                                foreach($param as $arg_key => $arg){
                                    $count = 0;
                                    $GLOBALS['url_array_instances'][$url] = str_ireplace('<'.$arg_key.'>', $arg, $GLOBALS['url_array_instances'][$url], $count);
                                    if(!$count)$_args[$arg_key] = $arg;
                                }
                                $GLOBALS['url_array_instances'][$url] = preg_replace('/<\w+>/', '', $GLOBALS['url_array_instances'][$url]). (!empty($_args) ? '?'.http_build_query($_args) : '');
                            }

                            if(0!==stripos($GLOBALS['url_array_instances'][$url], $GLOBALS['http_scheme'])){
                                $GLOBALS['url_array_instances'][$url] = $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') .'/'.$GLOBALS['url_array_instances'][$url];
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
    static public function dump($var, $exit = false){
        if(is_bool($var)){
            if($var)$var='(bool)true';
            else $var='(bool)false';
        }
        $output = print_r($var, true);
        if(!$GLOBALS['debug'])return error_log(str_replace("\n", '', $output));
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><style>pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857143;color: #333;word-break: break-all;word-wrap: break-word;background-color:#f5f5f5;border: 1px solid #ccc;border-radius: 4px;}</style></head><body><div align=left><pre>" .htmlspecialchars($output). "</pre></div></body></html>";
        if($exit) exit();
        else return '';
    }

    /**
     * @param $name
     * @return false|int
     */
    static public function is_available_classname($name){
        return preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $name);
    }

    /**
     * @param null $name
     * @param null $default
     * @param bool $trim
     * @return mixed|string|null
     */
    static public function arg($name = null, $default = null, $trim = false) {
        if(!isset($_REQUEST['m']))$_REQUEST['m']='index';
        if($name){
            if(!isset($_REQUEST[$name]))return $default;
            $arg = $_REQUEST[$name];
            if($trim)$arg = trim($arg);
        }else{
            $arg = $_REQUEST;
        }
        return $arg;
    }
}
