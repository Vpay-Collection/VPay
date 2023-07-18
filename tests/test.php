<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * File test.php
 * Created By ankio.
 * Date : 2023/7/14
 * Time : 21:15
 * Description :
 */


function extractUsedFunctions($directory)
{
    $functions = [];

    // 打开目录
    $handle = opendir($directory);

    // 遍历目录中的文件和子目录
    while (false !== ($file = readdir($handle))) {
        // 排除当前目录和上级目录
        if ($file != '.' && $file != '..') {
            $path = $directory . '/' . $file;

            // 如果是目录，递归处理
            if (is_dir($path)) {
                $functions = array_merge($functions, extractUsedFunctions($path));
            } else {
                // 如果是 PHP 文件，提取函数
                if (pathinfo($path, PATHINFO_EXTENSION) == 'php') {
                    $fileContent = file_get_contents($path);
                    $pattern = '/(?<![->|::|\w.])\s*\b([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(/';
                    preg_match_all($pattern, $fileContent, $matches);


                    if (isset($matches[1])) {
                        foreach ($matches[1] as $item){
                            if(function_exists($item)){
                                $functions[] = $item;
                            }
                        }
                    }
                }
            }
        }
    }

    // 关闭目录句柄
    closedir($handle);
    $functions = array_unique($functions);
    return $functions;
}

$directory = __DIR__."/../src";
$usedFunctions = extractUsedFunctions($directory);

// 输出整理成 PHP 数组形式的函数列表
echo '<pre>';
print_r($usedFunctions);
$phpCode = '<?php' . PHP_EOL;
$phpCode .= '$functions = ' . var_export($usedFunctions, true) . ';';

// 将代码写入文件
$filename = 'output.php';
file_put_contents($filename, $phpCode);
echo '</pre>';

