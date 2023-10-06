<?php
/*
 *  Copyright (c) 2023. Ankio. All Rights Reserved.
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


    public $percent = 0.1;
    private $src;
    private $imageinfo;
    private $image;

    public function __construct($src)
    {

        $this->src = $src;

    }

    public static function compress(string $tmp_name)
    {
        $image = new ImageCompress($tmp_name);
        if ($image->openImage()) {
            $image->thumpImage();
            $image->saveImage();
        }

        unset($image);
    }

    /**
     * 打开图片
     */
    public function openImage(): bool
    {

        list($width, $height, $type, $attr) = getimagesize($this->src);
        $this->imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );
        if ($this->imageinfo['type'] === "gif") {
            return false;
        }

        $fun = "imagecreatefrom" . $this->imageinfo['type'];
        if (function_exists($fun)) {
            $this->image = $fun($this->src);
            return true;
        }
        return false;

    }

    /**
     * 操作图片
     */
    public function thumpImage(): void
    {

        if ($this->imageinfo['width'] > 1024) {
            $this->percent = 1024 / $this->imageinfo['width'];
        }
        $new_width = intval($this->imageinfo['width'] * $this->percent);
        $new_height = intval($this->imageinfo['height'] * $this->percent);
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo['width'], $this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    /**
     * 保存图片到硬盘
     */
    public function saveImage(): void
    {

        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image, $this->src);

    }


    /**
     * 销毁图片
     */
    public function __destruct()
    {

        $this->image && imagedestroy($this->image);
    }

}