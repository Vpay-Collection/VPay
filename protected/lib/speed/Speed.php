<?php

namespace app;

use app\lib\speed\mvc\Controller;

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
                $rule = '/' . str_ireplace(
                        array('\\\\', $GLOBALS['http_scheme'], '/', '<', '>', '.'),
                        array('', '', '\/', '(?P<', '>[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+)', '\.'), $rule) . '/u';
                $rule = str_replace('\/\/','\/',$rule);
                
                if (preg_match($rule, strtolower($GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI'])), $matchs)) {
                    $route = explode("/", strtolower($mapper));
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
        $controller_name = 'app\\controller\\' . $__module . '\\' . $controller_name;

        if (!self::is_available_classname($__controller)) Error::_err_router("Err: Controller '$controller_name' is not correct!");
        if (!class_exists($controller_name, true)) Error::_err_router("Err: Controller '$controller_name' is not exists!");
        if (!method_exists($controller_name, $action_name)) Error::_err_router("Err: Method '$action_name' of '$controller_name' is not exists!");

        /**
         * @var $controller_obj Controller
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


}
