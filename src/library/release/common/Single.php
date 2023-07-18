<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace library\release\common;

use cleanphp\file\File;

/**
 * Package: release
 * Class Single
 * Created By ankio.
 * Date : 2023/1/7
 * Time : 17:45
 * Description :
 */
class Single
{
    private $fp;

    public function __construct($fileName)
    {
        $file = dirname(BASE_DIR) . DIRECTORY_SEPARATOR . "dist" . DIRECTORY_SEPARATOR . "$fileName.php";
        File::del($file);
        $this->fp = fopen($file, "w+");
    }

    public function __destruct()
    {
        fclose($this->fp);
    }

    function run($new)
    {

        fwrite($this->fp, '<?php
$randKey = "');
        $token = uniqid("key1_");
        fwrite($this->fp, uniqid("key2_"));
        fwrite($this->fp, '";$token="' . $token);
        fwrite($this->fp, '";
        $path = dirname(__FILE__)."/tmp_".md5($randKey)."/";
        function deldir($dir) {$dh = opendir($dir);while ($file = readdir($dh)) {if($file != "." && $file!="..") {$fullpath = $dir."/".$file;if(!is_dir($fullpath)) {unlink($fullpath);} else {deldir($fullpath);}}}closedir($dh);if(rmdir($dir)) {return true;} else {return false;}}
        if(isset($_GET["token"])&&$token===$_GET["token"]){
            deldir($path);
            unlink(__FILE__);
            exit("bye!");
        }
$path = dirname(__FILE__)."/tmp_".md5($randKey)."/";
if(!is_dir($path)){
mkdir($path,0777,true);
$codes = [
'
        );

        File::traverseDirectory($new, function ($f) {
            fwrite($this->fp, '"' . str_replace(dirname(BASE_DIR) . DIRECTORY_SEPARATOR . "dist" . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR, "", $f) . '"=>"' . base64_encode(file_get_contents($f)) . '",');

        });

        fwrite($this->fp, '

];
foreach($codes as $item=>$data){

$p =  pathinfo($path.$item,PATHINFO_DIRNAME);
if(!is_dir($p)){
mkdir($p,0777,true);
}
file_put_contents($path.$item,base64_decode($data));
}
}
include $path."public/index.php";
'
        );
        File::del($new);
        echo "\n[信息]PHP痕迹清除密钥：$token ";
        echo "\n[信息]单一文件打包完成. ";
    }


}