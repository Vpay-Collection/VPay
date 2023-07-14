<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: server\optimization\js
 * Class CompressJs
 * Created By ankio.
 * Date : 2023/3/16
 * Time : 12:22
 * Description :
 */

namespace library\release\js;

use Exception;

class CompressJs
{
    static function compress($file)
    {
        if (substr($file, (strlen($file) - 6), 6) == "min.js") return;
        $js = file_get_contents($file);

        try {
            file_put_contents($file, (new JavascriptPacker($js, 0, true, true))->pack());
        } catch (Exception $e) {
        }
    }
}