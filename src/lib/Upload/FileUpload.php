<?php
namespace app\lib\Upload;
use app\core\debug\Log;
use app\core\utils\FileUtil;
use app\core\utils\StringUtil;

/**
 * Class FileUpload
 * Created By ankio.
 * Date : 2022/1/14
 * Time : 10:57 上午
 * Description : 文件上传处理类
 */

class FileUpload {



    private string $path = APP_DIR."/storage/upload";          //上传文件保存的路径

    private array $allowType = ['jpg','gif','png','jpeg']; //设置限制上传文件的类型

    private int $maxSize = 1000000;           //限制文件上传大小（字节）

    private string $originName="";              //源文件名

    private string $tmpFileName="";              //临时文件名

    private string $fileType="";               //文件类型(文件后缀)

    private int $fileSize=0;               //文件大小

    private  $newFileName="";              //新文件名

    private int $errorNum = 0;             //错误号

    private  $errorMsg="";             //错误报告消息


    /**
     * 用于设置成员属性（$path, $allow_type,$max_size, $is_randname）
     * 可以通过连贯操作一次设置多个属性值
     *@param string $key  成员属性名(不区分大小写)
     *@param  mixed  $val  为成员属性设置的值
     */
    function set(string $key, $val): FileUpload
    {
        $key = strtolower($key);
        if( array_key_exists( $key, get_class_vars(get_class($this)))){
            $this->setOption($key, $val);
        }
        return $this;
    }

    /**
     * 检测是否存在PHP可执行代码
     * @param $path string 文件地址
     * @return bool
     */
    public function isPHP(string $path): bool
    {
        if(file_exists($path)){
            $data=file_get_contents($path);
            if(StringUtil::get(strtolower($data))->contains("<?php")){
                FileUtil::delFile($path);
                file_put_contents($this->path.DS.md5($path).".phpFile",base64_encode($data));
                return true;
            }
        }
        return false;
    }

    public function getFile($fileName){
       $strUtil=StringUtil::get($fileName);
       $str=$strUtil->findEnd("/");
       $path=$this->path.DS.$str;
        if(file_exists($path)){
            $data=file_get_contents($path);
            if(StringUtil::get(strtolower($data))->contains("<?php")){
                FileUtil::delFile($path);
                file_put_contents($path.".phpFile",base64_encode($data));
                return "";
            }
            return $data;
        }
        return "";
    }


    /**
     * 调用该方法上传文件
     * @param $fileField
     * @return bool        如果上传成功返回数true
     */
    function upload($fileField): bool
    {

        $return = true;
        /* 检查文件路径是滞合法 */
        if( !$this->checkFilePath() ) {
            $this->errorMsg = $this->getError();
            return false;
        }

        /* 将文件上传的信息取出赋给变量 */

        $name = $_FILES[$fileField]['name'];
        $tmp_name = $_FILES[$fileField]['tmp_name'];
        $size = $_FILES[$fileField]['size'];
        $error = $_FILES[$fileField]['error'];

        /* 如果是多个文件上传则$file["name"]会是一个数组 */

        if(is_Array($name)){
            $errors=array();
            /*多个文件上传则循环处理 ， 这个循环只有检查上传文件的作用，并没有真正上传 */
            for($i = 0; $i < count($name); $i++){
                /*设置文件信息 */
                if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i] )) {

                    if(!$this->checkFileSize() || !$this->checkFileType()){
                        $errors[] = $this->getError();
                        $return=false;
                    }
                }else{
                    $errors[] = $this->getError();
                    $return=false;
                }
                /* 如果有问题，则重新初使化属性 */

                if(!$return)
                    $this->setFiles();
            }

            if($return){
                /* 存放所有上传后文件名的变量数组 */
                $fileNames = array();
                /* 如果上传的多个文件都是合法的，则通过销魂循环向服务器上传文件 */
                for($i = 0; $i < count($name); $i++){
                    if($this->setFiles($name[$i], $tmp_name[$i], $size[$i], $error[$i] )) {
                        $this->setNewFileName();
                        $result=$this->copyFile();
                        if(!$result){
                            $errors[] = $this->getError();
                            $return = false;
                        }
                        $fileNames[] = $this->newFileName;

                    }

                }

                $this->newFileName = $fileNames;

            }

            $this->errorMsg = $errors;

            /*上传单个文件处理方法*/

        } else {
            $return = false;
            /* 设置文件信息 */
            if($this->setFiles($name,$tmp_name,$size,$error)) {
                /* 上传之前先检查一下大小和类型 */
                if($this->checkFileSize() && $this->checkFileType()){
                    /* 为上传文件设置新文件名 */
                    $this->setNewFileName();
                    /* 上传文件  返回0为成功， 小于0都为错误 */
                    $return = $this->copyFile();
                }
            }
            $this->errorMsg=$this->getError();
        }
        return $return;

    }



    /**
     * 获取上传后的文件名称
     * @param  void   没有参数
     * @return ?string|array 上传后，新文件的名称， 如果是多文件上传返回数组
     */

    public function getFileName(): string
    {
        return $this->newFileName;
    }

    /**
     * 获取新的文件上传路径
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->path.DS.$this->newFileName;
    }

    /**

     * 上传失败后，调用该方法则返回，上传出错信息
     * @param  void   没有参数
     * @return string  返回上传文件出错的信息报告，如果是多文件上传返回数组
     */
    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }


    /**
     * 获取错误信息
     * @return string
     */
    private function getError(): string
    {

        $str = "上传文件 -> {$this->originName} <- 时出错 : ";
        switch ($this->errorNum) {
            case 4: $str .= "没有文件被上传"; break;
            case 3: $str .= "文件只有部分被上传"; break;
            case 2: $str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值"; break;
            case 1: $str .= "上传的文件超过了php.ini中upload_max_file_size选项限制的值"; break;
            case -1: $str .= "未允许类型"; break;
            case -2: $str .= "文件过大,上传的文件不能超过{$this->maxSize}个字节"; break;
            case -3: $str .= "上传失败"; break;
            case -4: $str .= "建立存放上传文件目录失败，请重新指定上传目录"; break;
            case -5: $str .= "必须指定上传文件的路径"; break;
            case -6: $str .= "发现病毒！";break;
            case 0: $str .= "无错误";break;
            default: $str .= "未知错误";
        }
        return $str;

    }


    /**
     * 设置和$_FILES有关的内容
     * @param string $name
     * @param string $tmp_name
     * @param int $size
     * @param int $error
     * @return bool
     */

    private function setFiles(string $name="", string $tmp_name="", int $size=0, int $error=0): bool
    {
        $this->errorNum= $error;
        if($error)
            return false;
        $this->originName= $name;
       $this->tmpFileName=$tmp_name;
        $aryStr = explode(".", $name);
       $this->fileType=strtolower($aryStr[count($aryStr)-1]);
       $this->fileSize= $size;
        return true;
    }


    /**
     * 为单个成员属性设置值
     * @param $key
     * @param $val
     */

    private function setOption($key, $val) {
        $this->$key = $val;
    }


    /**
     * 设置上传后的文件名称
     */

    private function setNewFileName() {
        $this->newFileName =  $this->proRandName();
    }


    /**
     * 检查上传的文件是否是合法的类型
     * @return bool
     */
    private function checkFileType(): bool
    {
        if (in_array(strtolower($this->fileType), $this->allowType)) {
            return true;
        }else {
           $this->errorNum= -1;
            return false;
        }
    }


    /**
     * 检查上传的文件是否是允许的大小
     * @return bool
     */
    private function checkFileSize(): bool
    {
        if ($this->fileSize > $this->maxSize) {
           $this->errorNum= -2;
            return false;
        }else{
            return true;
        }
    }


    /**
     * 检查是否有存放上传文件的目录
     * @return bool
     */

    private function checkFilePath(): bool
    {
        if(empty($this->path)){
           $this->errorNum= -5;
            return false;
        }
        if (!file_exists($this->path) || !is_writable($this->path)) {
            if (!@mkdir($this->path, 0755)) {
               $this->errorNum= -4;
                return false;
            }
        }
        return true;
    }


    /**
     * 设置随机文件名
     * @return string
     */
    private function proRandName(): string
    {
        $fileName = date('YmdHis')."_".rand(100,999);
        return $fileName.'.'.$this->fileType;
    }


    /**
     * 复制上传文件到指定的位置
     * @return bool
     */

    private function copyFile(): bool
    {

        if($this->errorNum==0) {
            $path = rtrim($this->path, '/').'/';
            $path .= $this->newFileName;
            if($this->isPHP($this->tmpFileName)){
               $this->errorNum= -6;
                return false;
            }
            //优化奇奇怪怪的文件拷贝
            if (move_uploaded_file($this->tmpFileName, $path)||copy($this->tmpFileName, $path)) {
                $this->errorNum = 0;
                return true;
            }else{
               $this->errorNum = -3;
               return false;
            }
        } else {
            return false;
        }

    }

}