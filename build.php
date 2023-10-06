<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * File build.php
 * Created By ankio.
 * Date : 2023/10/6
 * Time : 21:34
 * Description :
 */

use cleanphp\release\js\CompressJs;
include "src/cleanphp/file/File.php";
include "src/cleanphp/release/js/ParseMaster.php";
include "src/cleanphp/release/js/JavascriptPacker.php";
include "src/cleanphp/release/js/CompressJs.php";
const APP_DIR = __DIR__.DIRECTORY_SEPARATOR."src";
 function compress(): void
{
    $dir = APP_DIR . "/app";
    $file = $dir . "/public/app.min.js";
    $array = [
        "/public/pack/jquery.min.js",
        "/public/mdb/js/mdb.min.js",
        "/public/pack/theme.js",
        "/public/pack/mdbAdminPlugins.js",
        "/public/pack/mdbAdmin.js",
        "/public/pack/loading.js",
        "/public/pack/form.js",
        "/public/pack/requests.js",
        "/public/pack/toast.js",
        "/public/pack/alert.js",
        "/public/pack/modal.js",
        "/public/pack/resource.js",
        "/public/pack/log.js",
        "/public/pack/route.js",
    ];


    foreach (scandir($dir . "/public/pack/routes") as $item) {
        if (str_starts_with($item, ".")) {
            continue;
        }
        $array[] = "/public/pack/routes/$item";
    }

    foreach (scandir($dir . "/public/pack/frames") as $item) {
        if (str_starts_with($item, ".")) {
            continue;
        }
        $array[] = "/public/pack/frames/$item";
    }
    $array[] = "/public/main.js";

    combineFilesStream($dir, $array, $file);

}

 function combineFilesStream($dir, $files, $outputPath): void
{


    $outputFile = fopen($outputPath, 'w'); // 打开输出文件进行写入

    if (!$outputFile) {
        die("Unable to open the output file for writing.");
    }

    $tempDir = __DIR__.DIRECTORY_SEPARATOR."temp";

    foreach ($files as $file) {
        $file__ = $dir . $file;

        if (is_file($file__)) {
            $temp = $tempDir.$file;
            \cleanphp\file\File::mkDir( dirname($temp));
            copy($file__,$temp);
            CompressJs::compress($temp);
            $inputFile = fopen($temp, 'r'); // 打开当前文件进行读取
            if ($inputFile) {
                while (!feof($inputFile)) {
                    $buffer = fread($inputFile, 4096); // 读取4KB
                    fwrite($outputFile, $buffer);      // 写入到输出文件
                }
                fwrite($outputFile, "\n"); // 在每个文件后面添加一个换行符，确保代码不会混在一起
                fclose($inputFile);
            }
        } else {
            echo "File not found: " . $file__ . "\n";
        }
    }

    \cleanphp\file\File::del($tempDir);

    fclose($outputFile);
}

compress();