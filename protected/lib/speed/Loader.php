<?php

namespace app;
class Loader
{
    public static function register()
    {
        spl_autoload_register('app\\Loader::autoload', true, true);
        $data = scandir(APP_LIB);
        foreach ($data as $value){
            if($value != '.' && $value != '..'){
                $file = APP_LIB . DS . $value . DS . 'autoload.php';
                if (file_exists($file)) include $file;//注册第三方库的自动加载
            }
        }
    }

    public static function autoload($realClass)
    {

        $namespaceList=array(
            'app'=>'protected/lib/speed',//重新定义命名空间指向
            'app/config'=>'protected'
        );

        $classArr=self::getClass($realClass);
        $class = $classArr['class']. '.php';
        $namespace = $classArr['namespace'];

        if(isset($namespaceList[$namespace])){
            $file=APP_DIR . DS .$namespaceList[$namespace].DS . $class ;
        }else{
            $file = APP_DIR . DS . 'protected' . DS.str_replace('app/','',$namespace).DS . $class ;
        }
        if (file_exists($file)){
            include $file;
            if(isset($GLOBALS['debug'])&&$GLOBALS['debug'])
                logs('[Loader]Load Class "'.$realClass.'"','info');
            return;
        }
        logs('[Loader]We Can\'t find this class "'.$realClass.'"('.$file.') in default Loader , You may have loaded it in another loader','warn');
    }
    public static function getClass($class){
        if(strpos($class,'.'))Error::err('[Loader]"'.$class.'" is not a valid class name！');
        $name=explode('\\',$class);
        $size=sizeof($name);
        $namespace='';
        for($i=0;$i<$size-1;$i++){
            $namespace.=$name[$i].(($i<$size-2)?'/':'');
        }
        return array('namespace'=>$namespace,'class'=>$name[$size-1]);
    }

}