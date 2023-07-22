<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * File build_bt.php
 * Created By ankio.
 * Date : 2023/7/22
 * Time : 08:54
 * Description :
 */

use cleanphp\file\File;

if(empty($removed)){
    $removed = [];
}
if(empty($base)){
    $base = __DIR__;
}
if(empty($new)){
    $new = __DIR__;
}
File::copy($base.DIRECTORY_SEPARATOR."bt", $new.DIRECTORY_SEPARATOR);