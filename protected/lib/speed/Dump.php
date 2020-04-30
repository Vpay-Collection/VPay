<?php
namespace app\lib\speed;
use ReflectionClass;

class Dump{
    public function reconstructDump() {
        $args   = func_num_args();
        for($i = 0;$i < $args; $i ++) {
            $param = func_get_arg($i);
            switch(gettype($param)) {
                case 'NULL' :
                    echo '<font color=\'#3465a4\'>null</font>';
                    break;
                case 'boolean' :
                    echo "<small>boolean</small> <font color='#75507b'>".($param?'true':'false')."</font>";
                    break;
                case 'integer' :
                    echo "<small>int</small> <font color='#4e9a06'>$param</font>";
                    break;
                case 'double' :
                    echo "<small>float</small> <font color='#f57900'>$param</font>";
                    break;
                case 'string' :
                    $this->dumpString($param);
                    break;
                case 'array' :
                    $this->dumpArr($param);
                    break;
                case 'object' :
                    $this->dumpObj($param);
                    break;
                case 'resource' :
                    echo '<font color=\'#3465a4\'>resource</font>';
                    break;
                default :
                    echo '<font color=\'#3465a4\'>unknow type</font>';
                    break;
            }
        }
    }


    private function dumpString($param) {

        $str = sprintf("<small>string</small> <font color='#cc0000'>'%s'</font> <i>(length=%d)</i> \r\n ",htmlspecialchars(chkCode($param)),strlen($param));
        echo $str;
    }

    private function dumpArr($param,$i=0) {

        $len = count($param);
        $space='';
        for($m=0;$m<$i;$m++)
            $space.= "    ";
        $i++;
        echo $space."<b>array</b> <i>(size=$len)</i> \r\n";
        if($len===0)
            echo $space."  <i><font color=\"#888a85\">empty</font></i> \r\n";
        foreach($param as $key=>$val) {
            if(is_array($val)) {
                echo $space.sprintf("  %s <font color='#888a85'>=&gt;</font> \r\n",gettype($key)==='string'?"'".$key."'":$key);
                $this->dumpArr($val,$i);
            } else {
                echo $space.sprintf("  %s <font color='#888a85'>=&gt;</font> <small>%s</small> <font color='%s'>%s</font>\r\n",gettype($key)==='string'?"'".$key."'":$key,gettype($val)==='NULL'?'':gettype($val),$this->getcolor(gettype($val)),$this->getshow($val));
            }
        }
        //echo "\r\n";
    }
    private function getshow($val){
        switch(gettype($val)){
            case 'string':return "'".htmlspecialchars(chkCode($val))."'</font><font><i> (length=".strlen($val).")</i>";break;
            case 'boolean':return $val?'true':'false';break;
            case 'NULL':return 'null';
            default:return $val;
        }
    }
    private function getcolor($type){
        switch($type) {
            case 'boolean' :
                return '#75507b';
                break;
            case 'integer' :
                return '#4e9a06';
                break;
            case 'double' :
                return '#f57900';
                break;
            case 'string' :
                return '#cc0000';
                break;
            default :
                return '#3465a4';
                break;
        }
    }

    private function dumpObj($param) {
        $className = get_class($param);
        if($className=='stdClass'&&$result=json_encode($param)){
            $this->dumpArr(json_decode($result,true));
            return;
        }
        try {
            $reflect = new ReflectionClass($param);
            $prop = $reflect->getDefaultProperties();
            echo sprintf("<b>Object</b> <i>(%s)</i> [1]<i>(size=%d)</i> \r\n",$className,count($prop));

            foreach($prop as $key=>$val) {
                echo  sprintf("  %s <font color='#888a85'>=&gt;</font> ",gettype($key)==='string'?"'".$key."'":$key);
                $this->reconstructDump($val);
                echo "\r\n";
            }
            echo "\r\n";
        } catch (\ReflectionException $e) {

        }

    }

}