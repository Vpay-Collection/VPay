<?php
namespace Speed;
namespace lib\speed;
class Loader
{
    public static function register()
    {
        spl_autoload_register('lib\\speed\\Loader::autoload', true, true);
    }
    public static function autoload($class)
    {
        $class=str_replace('\\','/',$class);
        $file = APP_DIR.DS.'protected'.DS.$class.'.php';

       // var_dump($file.'<br >',file_exists($file));
        if(file_exists($file))include $file;
        else{

            $class=str_replace('Speed/','',$class);
            $file = APP_DIR.DS.'protected'.DS.$class.'.php';
            //echo $file.'<br >';
            if(file_exists($file))include $file;
        }
    }

}