<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * 图片压缩类：通过缩放来压缩。
 * 如果要保持源图比例，把参数$percent保持为即可。
 * 即使原比例压缩，也可大幅度缩小。数码相机M图片。也可以缩为KB左右。如果缩小比例，则体积会更小。
 *
 * 结果：可保存、可直接显示。
 */

namespace app\utils;
class ImageCompress
{
    private $image;
    private $imageinfo;

    public static function compress(string $tmp_name)
    {
        (new ImageCompress())->compressImg($tmp_name, 1, $tmp_name);
    }

    /**
     * [compressImg 图片压缩操作]
     * @param  [type]  $url      [图片路径]
     * @param float $percent [缩放比例（或缩放后尺寸）]
     * @param string $saveName [保存文件名称（可不带扩展名，用源图扩展名）]
     * @return bool [type]            [description]
     */
    public function compressImg($url, float $percent = 1, string $saveName = ''): bool
    {
        $re = $this->_openImage($url);
        if ($this->imageinfo) {
            $this->_thumpImage($percent);
            if (!empty($saveName)) {
                $this->_saveImage($url, $saveName);  //保存
            } else {
                $url != $saveName && $this->_showImage();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 内部:打开图片
     */
    private function _openImage($url)
    {
        $image = @file_get_contents($url); // 将 logo 读取到字符串中
        if (!$image)
            return false;

        $imagedetail = getimagesizefromstring($image);
        list($width, $height, $type, $attr) = $imagedetail;
        if (!$width)
            return false;

        $this->imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );
        $this->image = imagecreatefromstring($image); // 从字符串中的图像流新建一图像
    }

    /**
     * 内部:操作图片
     */
    private function _thumpImage($percent)
    {
        if ($percent > 1 && $percent < $this->imageinfo['height'] && $percent < $this->imageinfo['width']) {
            if ($this->imageinfo['height'] < $this->imageinfo['width']) {
                $percent = $percent / $this->imageinfo['height'];
            } else {
                $percent = $percent / $this->imageinfo['width'];
            }
        }
        $ratio = min($percent, 1);
        $new_width = $this->imageinfo['width'] * $ratio;
        $new_height = $this->imageinfo['height'] * $ratio;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo['width'], $this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    /**
     * 保存图片到硬盘:
     * @param string $dstImgName 1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     */
    private function _saveImage($url, string $dstImgName): void
    {
        if (empty($dstImgName)) {
            return;
        }
        $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp', '.gif'];   //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, ".");
        $sourseExt = strrchr($url, ".");
        if (!empty($dstExt)) $dstExt = strtolower($dstExt);
        if (!empty($sourseExt)) $sourseExt = strtolower($sourseExt);
        //有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourseExt) && in_array($sourseExt, $allowImgs)) {
            $dstName = $dstImgName . $sourseExt;
        } else {
            $dstName = $dstImgName . $this->imageinfo['type'];
        }
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image, $dstName);
    }

    /**
     * 输出图片:保存图片则用saveImage()
     */
    private function _showImage()
    {
        header('Content-Type: image/' . $this->imageinfo['type']);
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image);
    }

    /**
     * 销毁图片
     */
    public function __destruct()
    {
        $this->image && imagedestroy($this->image);
    }
}