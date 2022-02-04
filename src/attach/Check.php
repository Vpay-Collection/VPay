<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Check
 * Created By ankio.
 * Date : 2022/2/3
 * Time : 8:11 下午
 * Description :
 */

namespace app\attach;

use app\core\utils\StringUtil;

class Check{
    private $conf;
    private $env;
    public function __construct($conf,$env)
    {
        $this->conf =$conf;
        $this->env =$env;
    }

    public function env()
    {

        $env_items[] = array('name' => 'PHP伪静态', 'min' => '需要', 'good' => '需要', 'cur' => $this->rewrite()?'已配置':'未配置', 'status' =>  $this->rewrite()?1:0);


        $env_items[] = array('name' => '操作系统', 'min' =>  $this->env['os']['min'], 'good' => $this->env['os']['good'], 'cur' => PHP_OS, 'status' => $this->env['os']['min']==='不限'?1:(stripos(PHP_OS,$this->env['os']['min'])?1:0));

        $env_items[] = array('name' => 'PHP版本', 'min' => $this->env['php']['min'], 'good' => $this->env['php']['good'], 'cur' => PHP_VERSION, 'status' => (PHP_VERSION < intval($this->env['php']['min']) ? 0 : 1));

        $env_items[] = array('name' => '附件上传', 'min' => $this->env['upload']['min'], 'good' => $this->env['upload']['good'], 'cur' => ini_get('upload_max_filesize'), 'status' => intval(ini_get('upload_max_filesize'))<intval($this->env['upload']['min'])?0:1);

        $disk_place = function_exists('disk_free_space') ? floor(disk_free_space(APP_DIR) / (1024 * 1024)) : 0;
        $env_items[] = array('name' => '磁盘空间', 'min' => $this->env['disk']['min'], 'good' => $this->env['disk']['good'], 'cur' => empty($disk_place) ? '未知' : $disk_place . 'M', 'status' => $disk_place < $this->env['disk']['min'] ? 0 : 1);

        return $env_items;
    }

    /**
     * file check
     */
    public function dirfile()
    {
        $dirfile_items = [];

        foreach ($this->conf["dirfile"] as $key => $item) {
            $item_path = '/' . $item['path'];
            $dirfile_items[$key]["path"]=$item['path'];
            if ($item['type'] === 'dir') {
                if (!$this->dir_writeable(APP_DIR . $item_path)) {
                    if (is_dir(APP_DIR . $item_path)) {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nodir';
                    }
                } else {
                    $dirfile_items[$key]['status'] = 1;
                    $dirfile_items[$key]['current'] = '+r+w';
                }
            } else {
                if (file_exists(APP_DIR . $item_path)) {
                    if (is_writable(APP_DIR . $item_path)) {

                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                    } else {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    }
                } else {
                    if ($fp = @fopen(APP_DIR . $item_path, 'wb+')) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                        @fclose($fp);
                        @unlink(APP_DIR . $item_path);
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nofile';
                    }
                }
            }
        }
        return $dirfile_items;
    }

    /**
     * dir is writeable
     * @param $dir
     * @return int
     */
    private function dir_writeable($dir): int
    {
        $writeable = 0;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755);
        } else {
            @chmod($dir, 0755);
        }
        if (is_dir($dir)) {
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     * function is exist
     */
    public function func(): array
    {
        $func_items = [];
        foreach ($this->conf["func"] as $key => $item) {
            $func_items[$key]["name"]=$item['name'];
            $func_items[$key]['status'] = function_exists($item['name']) ? 1 : 0;
        }
        return $func_items;
    }

    public function ext(): array
    {
        $ext_items =[];

        foreach ($this->conf["ext"] as $key => $item) {
            $ext_items[$key]["name"]=$item['name'];
            $ext_items[$key]['status'] = extension_loaded($item['name']) ? 1 : 0;
        }
        return $ext_items;
    }

    public function rewrite(): bool
    {
        $url= $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].'/index/install/checkOut';
        $result = file_get_contents($url, false, stream_context_create([
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ],
        ]));
        if(StringUtil::get($result)->startsWith("{"))return true;
        else return false;

    }

}