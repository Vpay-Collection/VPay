<?php

namespace MGQrCodeReader;

use cleanphp\base\Variables;

require_once('php-qrcode-detector-decoder/lib/QrReader.php');
require_once('Thumbnail.php');

class MGQrCodeReader
{

    private $imgPath;
    private $scaleSize = [100, 200, 300, 400, 500];

    public function __construct()
    {


        $this->imgPath = Variables::getCachePath();

    }

    public function setScaleSize($sizeArray)
    {
        if (is_array($sizeArray)) {
            $this->scaleSize = $sizeArray;
        }
    }

    public function read($url)
    {
        $text = '';
        $imagePath = false;
        $tempImg = false;
        try {
            $imgName = $this->getFromUri($url);
            $imagePath = $this->imgPath . $imgName;
            $tmpPath = $this->imgPath . 'tmp_' . $imgName;

            $success = false;
            $finder_count = 0;
            foreach ($this->scaleSize as $k => $size) {
                if ($success) {
                    break;
                }
                try {
                    $finder_count++;

                    $th = new Thumbnail();
                    $th->GenerateThumbnailScale($imagePath, $size, $tmpPath);
                    $tempImg = $th->getThumbnail();

                    $code = new \QrReader($tempImg, \QrReader::SOURCE_TYPE_FILE, false);
                    $text = $code->text();

                    if (!empty($text)) {
                        $success = true;
                        unlink($tempImg);
                        unlink($imagePath);
                    }
                } catch (\Exception $e) {
                    // Exception
                }
            }

        } catch (\Exception $e) {
            // Exception
        }
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
        if ($tempImg && file_exists($tempImg)) {
            unlink($tempImg);
        }
        return $text;
    }

    private function getFromUri($uri)
    {

        $fileLocalPath = false;
        $fileName = false;
        $max = 10;
        $valid = false;
        while (($max--) > 0) {
            $fileName = md5(mt_rand());
            $fileLocalPath = $this->imgPath . $fileName;
            if (!file_exists($fileLocalPath)) {
                $valid = true;
                break;
            } else {
                continue;
            }
        }
        if ($valid && $fileLocalPath) {
            $s = file_get_contents($uri);
            file_put_contents($fileLocalPath, $s, LOCK_EX);
        } else {
            throw new \Exception('Can Not Create Image File');
        }

        return $fileName;
    }

}
// end