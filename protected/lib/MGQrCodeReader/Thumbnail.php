<?php

namespace MGQrCodeReader;

use \Exception;

/**
 * @abstract 生成图片的缩略图，可以指定任意尺寸，生成的图片为png格式
 * @example
 * $file = 'test.png';
 * $th =new Thumbnail();
 * $th->GenerateThumbnail($file, 400, 500);
 *
 */
class Thumbnail{
    // $from 源图片
    private $from;
    // $name 缩略图的文件名
    private $name = '';
    // 原图宽
    private $rWidth;
    // 原图高
    private $rHeight;
    // 缩略图宽
    private $tWidth;
    // 缩略图高
    private $tHeight;
    // 实际缩放到的宽度
    private $width;
    // 实际缩放到的高度
    private $height;
    // sourceInfo
    private $sourceInfo;
 
    public function __construct(){
        try{
            if(!function_exists('gd_info')){
                throw new Exception('Must GD extension is enabled');
            }
        }catch(Exception $e){
            $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
            throw new Exception($msg);
        }
    }
    // $from 原图像
    // $width 生成的缩略图的宽
    // $height 生成缩略图的高
    // $name 生成的缩略图的文件名，不带后缀
    public function GenerateThumbnail($from, $width, $height, $name=''){
        try{
            if(!file_exists($from)){
                throw new Exception('File does not exist');
            }
            if($width <= 0){
                throw new Exception('The width is invalid');
            }
            if($height <= 0){
                throw new Exception('The height is invalid');
            }
            $this->from = $from;
            $this->getSource();

            $this->tWidth = $width;
            $this->tHeight = $height;
            if(!empty($name)){
                $this->name = $name;
            }else{
                $this->name = date('Ymd') . mt_rand(0, 9999);
            }
            $this->createThumbnail();
        }catch(Exception $e){
            $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
            throw new Exception($msg);
        }
    }

     // 等比缩放
    public function GenerateThumbnailScale($from, $scale, $name=''){
        try{
            if(!file_exists($from)){
                throw new Exception('File does not exist');
            }
            if($scale <= 0){
                throw new Exception('The scale is invalid');
            }
            $this->from = $from;
            $this->getSource();

            if($this->rWidth > $this->rHeight){
                $this->tWidth = $scale;
                $this->tHeight = intval(($scale/$this->rWidth)*$this->rHeight);
            }else{
                $this->tWidth = intval(($scale/$this->rHeight)*$this->rWidth);
                $this->tHeight = $scale;
            }

            if(!empty($name)){
                $this->name = $name;
            }else{
                $this->name = date('Ymd') . mt_rand(0, 9999);
            }
            $this->createThumbnail();
        }catch(Exception $e){
            $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
            throw new Exception($msg);
        }
    }
 
    public function getThumbnail(){
        return $this->name . '.png';
    }

    private function getSource(){
        $this->sourceInfo = getimagesize($this->from);
        //读取原始图像信息
        $this->rWidth = $this->sourceInfo[0];
        $this->rHeight = $this->sourceInfo[1];
    }
 
    // 生成缩略图文件
    private function createThumbnail(){
        try{
            //创建缩略图图像资源句柄
            $new_pic = imagecreatetruecolor($this->tWidth, $this->tHeight);
            //原图绘制到缩略图的x、y坐标
            $x = 0;
            $y = 0;
            //创建原始图像资源句柄
            $source_pic = '';
            if(!isset($this->sourceInfo[2])){
                throw new Exception('Invalid SourceInfo');
            }

            switch ($this->sourceInfo[2]){
                case 1: $source_pic = imagecreatefromgif($this->from); //gif
                    break;
                case 2: $source_pic = imagecreatefromjpeg($this->from); //jpg
                    break;
                case 3: $source_pic = imagecreatefrompng($this->from); //png
                    break;
                default:
                    try{
                        $source_pic = self::ImageCreateFromBMP($this->from); //try bmp
                    }catch (Exception $e){
                        throw new Exception('Does not support this type of image');
                    }
                break;
            }
            if($this->rWidth > $this->tWidth && $this->rHeight > $this->tHeight){
                //计算缩放后图像实际大小
                //原图宽高均比缩略图大
                $midw = ($this->rWidth - $this->tWidth) / $this->rWidth; //宽缩小的比例
                $midh = ($this->rHeight - $this->tHeight) / $this->rHeight; //高缩小的比例
                //那个缩小的比例大以那个为准
                if($midw > $midh){
                    $this->width = $this->tWidth;
                    $this->height = $this->rHeight - floor($this->rHeight * $midw);
                    $y = ($this->tHeight - $this->height) / 2;
                }else{
                    $this->width = $this->rWidth - floor($this->rWidth * $midh);
                    $this->height = $this->tHeight;
                    $x = ($this->tWidth - $this->width) / 2;
                }
            }elseif($this->rWidth < $this->tWidth && $this->rHeight < $this->tHeight){
                //原图宽高均比缩略图小
                $midw = ($this->tWidth - $this->rWidth) / $this->rWidth; //宽放大的比例
                $midh = ($this->tHeight - $this->rHeight) / $this->rHeight; //高放大的比例
                //那个放大的比例小以那个为准
                if($midw < $midh){
                    $this->width = $this->tWidth;
                    $this->height = $this->rHeight + floor($this->rHeight * $midw);
                    $y = ($this->tHeight - $this->height) / 2;
                }else{
                    $this->width = $this->rWidth + floor($this->rWidth * $midh);
                    $this->height = $this->tHeight;
                    $x = ($this->tWidth - $this->width) / 2;
                }
            }elseif($this->rWidth < $this->tWidth && $this->rHeight > $this->tHeight){
                //原图宽小于缩略图宽，原图高大于缩略图高
                $mid = ($this->rHeight - $this->tHeight) / $this->rHeight; //高缩小的比例
                $this->width = $this->rWidth - floor($this->rWidth * $mid);
                $this->height = $this->rHeight - floor($this->rHeight * $mid);
                $x = ($this->tWidth - $this->width) / 2;
                $y = ($this->tHeight - $this->height) / 2;
            }elseif($this->rWidth > $this->tWidth && $this->rHeight < $this->tHeight){
                //原图宽大于缩略图宽，原图高小于缩略图高
                $mid = ($this->rWidth - $this->tWidth) / $this->rWidth; //宽缩小的比例
                $this->width = $this->rWidth - floor($this->rWidth * $mid);
                $this->height = $this->rHeight - floor($this->rHeight * $mid);
                $x = ($this->tWidth - $this->width) / 2;
                $y = ($this->tHeight - $this->height) / 2;
            }else{
                throw new Exception('Resize error');
            }
            //给缩略图添加白色背景
            $bg = imagecolorallocate($new_pic, 255, 255, 255);
            imagefill($new_pic, 0, 0, $bg);
            //缩小原始图片到新建图片
            imagecopyresampled($new_pic, $source_pic, $x, $y, 0, 0, $this->width, $this->height, $this->rWidth, $this->rHeight);
            //输出缩略图到文件
            imagepng($new_pic, $this->name.'.png');
            imagedestroy($new_pic);
            imagedestroy($source_pic);
        }catch(Exception $e){
            $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
            throw new Exception($msg);
        }
    }

    public static function ImageCreateFromBMP($filename){    //自定义函数处理bmp图片
        if(!$f1=fopen($filename,"rb"))returnFALSE;
        $FILE=unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset",fread($f1,14));
        if($FILE['file_type']!=19778)returnFALSE;
        $BMP=unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
            '/Vvert_resolution/Vcolors_used/Vcolors_important',fread($f1,40));
        $BMP['colors']=pow(2,$BMP['bits_per_pixel']);
        if($BMP['size_bitmap']==0)$BMP['size_bitmap']=$FILE['file_size']-$FILE['bitmap_offset'];
        $BMP['bytes_per_pixel']=$BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2']=ceil($BMP['bytes_per_pixel']);
        $BMP['decal']=($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal']-=floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal']=4-(4*$BMP['decal']);
        if($BMP['decal']==4)$BMP['decal']=0;
        $PALETTE=array();
        if($BMP['colors']<16777216)
        {
            $PALETTE=unpack('V'.$BMP['colors'],fread($f1,$BMP['colors']*4));
        }
        $IMG=fread($f1,$BMP['size_bitmap']);
        $VIDE=chr(0);
        $res=imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P=0;
        $Y=$BMP['height']-1;
        while($Y>=0){
            $X=0;
            while($X<$BMP['width']){
                if($BMP['bits_per_pixel']==24){
                    $COLOR=unpack("V",substr($IMG,$P,3).$VIDE);
                }elseif($BMP['bits_per_pixel']==16){
                    $COLOR=unpack("n",substr($IMG,$P,2));
                    $COLOR[1]=$PALETTE[$COLOR[1]+1];
                }elseif($BMP['bits_per_pixel']==8){
                    $COLOR=unpack("n",$VIDE.substr($IMG,$P,1));
                    $COLOR[1]=$PALETTE[$COLOR[1]+1];
                }elseif($BMP['bits_per_pixel']==4){
                    $COLOR=unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if(($P*2)%2==0)$COLOR[1]=($COLOR[1]>>4);else$COLOR[1]=($COLOR[1]&0x0F);
                    $COLOR[1]=$PALETTE[$COLOR[1]+1];
                }elseif($BMP['bits_per_pixel']==1){
                    $COLOR=unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if(($P*8)%8==0)$COLOR[1]=$COLOR[1]>>7;
                    elseif(($P*8)%8==1)$COLOR[1]=($COLOR[1]&0x40)>>6;
                    elseif(($P*8)%8==2)$COLOR[1]=($COLOR[1]&0x20)>>5;
                    elseif(($P*8)%8==3)$COLOR[1]=($COLOR[1]&0x10)>>4;
                    elseif(($P*8)%8==4)$COLOR[1]=($COLOR[1]&0x8)>>3;
                    elseif(($P*8)%8==5)$COLOR[1]=($COLOR[1]&0x4)>>2;
                    elseif(($P*8)%8==6)$COLOR[1]=($COLOR[1]&0x2)>>1;
                    elseif(($P*8)%8==7)$COLOR[1]=($COLOR[1]&0x1);
                    $COLOR[1]=$PALETTE[$COLOR[1]+1];
                }else{
                    return false;
                }
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P+=$BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }
        fclose($f1);
        return $res;
    }

}