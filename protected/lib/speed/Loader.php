<?php

namespace lib\speed;
class Loader
{
    public static function register()
    {
        spl_autoload_register('lib\\speed\\Loader::autoload', true, true);
        $data = scandir(APP_LIB);
        foreach ($data as $value){
            if($value != '.' && $value != '..'){
                $file = APP_LIB . DS . $value . DS . 'autoload.php';
                if (file_exists($file)) include $file;//注册第三方库的自动加载
            }
        }
    }

    public static function autoload($class)
    {
        $class = str_replace('\\', '/', $class);
        $file = APP_DIR . DS . 'protected' . DS . $class . '.php';
        //echo $file.'<br >';
        if (file_exists($file)) include $file;
        else {

            $class = str_replace('Speed/', '', $class);
            $file = APP_DIR . DS . 'protected' . DS . $class . '.php';
            //echo $file.'<br >';
            if (file_exists($file)) include $file;
        }
    }

}