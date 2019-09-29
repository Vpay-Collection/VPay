<?php
//因为环境检查的复杂性，所以要单独写
$arr["dirfile"] = array(
    array('type' => 'dir', 'path' => 'protected/tmp'),
    array('type' => 'dir', 'path' => 'install'),
);

$arr["func"] = array(
    array('name' => 'json_decode'),
    array('name' => 'json_encode'),
    array('name' => 'urldecode'),
    array('name' => 'urlencode'),
    array('name' => 'openssl_encrypt'),
    array('name' => 'openssl_decrypt'),
    array('name' => 'file_get_contents'),
    array('name' => 'mb_convert_encoding'),
    array('name' => 'curl_init'),
);

$arr["ext"] = array(
    array('name' => 'curl'),
    array('name' => 'openssl'),
    array('name' => 'gd'),
    array('name' => 'json'),
    array('name' => 'session'),
    array('name' => 'PDO'),
    array('name' => 'iconv'),
    array('name' => 'hash'),
    array('name' => 'mysqli')
);

return $arr;
