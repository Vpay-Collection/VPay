<?php
define("APP_PATH", dirname(dirname(__FILE__)));  //定义根目录
define("ROOT_PATH", dirname(__FILE__));  //定义根目录
require ROOT_PATH."/include/function.php";
//引入检查文件
set_time_limit(0);   //设置运行时间
error_reporting(E_ALL & ~E_NOTICE);  //显示全部错误


define('DBCHARSET', 'UTF8');   //设置数据库默认编码
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Shanghai');
}

//判断是否安装过程序
if (is_file(ROOT_PATH.'/lock') && intval($_GET['step']) !== 5) {
    @header("Content-type: text/html; charset=UTF-8");
    echo "系统已经安装过了，如果要重新安装，那么请删除install目录下的lock文件";
    exit;
}

$html_title = 'V免签安装向导';
$html_header = file_get_contents(ROOT_PATH."/views/header.html");
$html_footer = file_get_contents(ROOT_PATH."/views/footer.html");

//传入的数据进行处理
input($_GET);
input($_POST);
function input(&$data)
{
    foreach ((array)$data as $key => $value) {
        if (is_string($value)) {
            if (!get_magic_quotes_gpc()) {
                $value = htmlentities($value, ENT_NOQUOTES);
                $value = addslashes(trim($value));
            }
        }
    }
}
//处理收到的step
if (!in_array($_GET['step'], array(1, 2, 3, 4, 5))) {
    $_GET['step'] = 0;//防止有人搞事情
}
switch ($_GET['step']) {
    case 1:
        $c=new check();
        $env_items=$c->env();//环境检查
        $dirfile_items=$c->dirfile();//文件读写权限检查
        $func_items=$c->func();//函数检查
        $ext_items=$c->ext();//php拓展检查

        break;

    case 3:
        if($_GET["iCheck"]==='mini'){
            deldir("demo");
        }
        $sql=new mysql();
        if(!$sql->install() &&$_SERVER['REQUEST_METHOD'] == 'POST' ? true : false){
            echo "<script>alert(\"".$sql->getErr()."\")</script>";
        }
        break;

    case 5:
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
        $sitepath = str_replace('install', "", $sitepath);
        $auto_site_url = strtolower($http_type . $_SERVER['HTTP_HOST'] . $sitepath);
        $fp = @fopen(ROOT_PATH.'/lock', 'wb+');
        @fclose($fp);
        break;
}

include(ROOT_PATH."/views/step_".$_GET['step'].".html");

function deldir($path){
    //如果是目录则继续
    if(is_dir($path)){
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $p = scandir($path);
        foreach($p as $val){
            //排除目录中的.和..
            if($val !="." && $val !=".."){
                //如果是目录则递归子目录，继续操作
                if(is_dir($path.$val)){
                    //子目录中操作删除文件夹和文件
                    deldir($path.$val.'/');
                    //目录清空后删除空文件夹
                    @rmdir($path.$val.'/');
                }else{
                    //如果是文件直接删除
                    unlink($path.$val);
                }
            }
        }
    }
}