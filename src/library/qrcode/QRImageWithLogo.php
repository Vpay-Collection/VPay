<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: library\qrcode
 * Class QRImageWithLogo
 * Created By ankio.
 * Date : 2023/5/5
 * Time : 17:35
 * Description :
 */

namespace library\qrcode;

use ErrorException;
use library\qrcode\src\Output\QRCodeOutputException;
use library\qrcode\src\Output\QRGdImage;

class QRImageWithLogo extends QRGdImage
{

    /**
     * @param string|null $file
     * @param string|null $logo
     *
     * @return string
     * @throws QRCodeOutputException|ErrorException
     */
    public function dump(string $file = null, string $logo = null): string
    {
        // set returnResource to true to skip further processing for now
        $this->options->returnResource = true;

        // of course, you could accept other formats too (such as resource or Imagick)
        // I'm not checking for the file type either for simplicity reasons (assuming PNG)
        if (!is_file($logo) || !is_readable($logo)) {
            throw new QRCodeOutputException('invalid logo');
        }

        // there's no need to save the result of dump() into $this->image here
        parent::dump($file);

        if ($this->options->addLogoSpace) {
            if (preg_match("/.*\.(gif)?$/", $logo)) {
                $im = imagecreatefromgif($logo);
            } elseif (preg_match("/.*\.(jpg|jpeg)?$/", $logo)) {
                $im = imagecreatefromjpeg($logo);
            } elseif (preg_match("/.*\.(png)?$/", $logo)) {
                $im = imagecreatefrompng($logo);
            } elseif (preg_match("/.*\.(bmp)?$/", $logo)) {
                $im = imagecreatefrombmp($logo);
            } else {
                throw new QRCodeOutputException('invalid logo ext');
            }


            // get logo image size
            $w = imagesx($im);
            $h = imagesy($im);

            // set new logo size, leave a border of 1 module (no proportional resize/centering)
            $lw = (($this->options->logoSpaceWidth - 2) * $this->options->scale);
            $lh = (($this->options->logoSpaceHeight - 2) * $this->options->scale);

            // get the qrcode size
            $ql = ($this->matrix->getSize() * $this->options->scale);

            // scale the logo and copy it over. done!
            imagecopyresampled($this->image, $im, (($ql - $lw) / 2), (($ql - $lh) / 2), 0, 0, $lw, $lh, $w, $h);
        }


        $imageData = $this->dumpImage();

        $this->saveToFile($imageData, $file);

        if ($this->options->imageBase64) {
            $imageData = $this->toBase64DataURI($imageData, 'image/' . $this->options->outputType);
        }

        return $imageData;
    }

}
