<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\main
 * Class Application
 * Created By ankio.
 * Date : 2023/4/24
 * Time : 12:39
 * Description :
 */

namespace app;


use app\utils\GithubUpdater;
use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\Cookie;
use cleanphp\base\EventManager;
use cleanphp\base\MainApp;
use cleanphp\base\Response;
use cleanphp\base\Route;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use cleanphp\engine\JsonEngine;
use cleanphp\engine\ViewEngine;
use library\task\TaskerManager;
use library\task\TaskerTime;

class Application implements MainApp
{


    /**
     * @inheritDoc
     */
    function onRequestArrive(): void
    {
        Session::getInstance()->start();//会话有效即可
        if (str_starts_with(Variables::get("__request_module__"),"api")) {
            EngineManager::setDefaultEngine(new JsonEngine(["code" => 0, "msg" => "OK", "data" => null, "count" => 0]));

        }

        if (empty(Cache::init(0,Variables::getCachePath('cleanphp',DS))->get("install.lock")) && Variables::get("__request_controller__") !== "install") {
            App::exit(EngineManager::getEngine()->render(302,"install","#!install"),true);
        }

        if(!TaskerManager::has("Github更新检测")){
            TaskerManager::add(TaskerTime::day(12,00),GithubUpdater::init("Vpay-Collection/Vpay"),"Github更新检测",-1);
        }

    }

    private function renderStatic(){
        //渲染静态资源
        $uriWithoutQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $path = Variables::getAppPath("public", $uriWithoutQuery);
        $raw = $path;
        if (is_dir($path)) {
            $path = $path . DS . "index.html";
        }

        if(!is_file($path)){
            $path =  $raw . ".html";
        }

        //  dumps($path);

        if (App::$debug && str_contains($path, "app.min.js")) {
            $this->compress();
        }

        if (is_file($path)) {
            Route::renderStatic($path);
        }

    }
    private function compress(): void
    {
        $dir = APP_DIR . "/app";
        $file = $dir . "/public/app.min.js";
        if( file_exists($file) && !App::$debug ){
            return;
        }
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

        $this->combineFilesStream($dir, $array, $file);

    }

    private function combineFilesStream($dir, $files, $outputPath): void
    {
        $outputFile = fopen($outputPath, 'w'); // 打开输出文件进行写入

        if (!$outputFile) {
            die("Unable to open the output file for writing.");
        }

        foreach ($files as $file) {
            $file = $dir . $file;
            //  echo $file . PHP_EOL;
            if (is_file($file)) {
                $inputFile = fopen($file, 'r'); // 打开当前文件进行读取
                if ($inputFile) {
                    while (!feof($inputFile)) {
                        $buffer = fread($inputFile, 4096); // 读取4KB
                        fwrite($outputFile, $buffer);      // 写入到输出文件
                    }
                    fwrite($outputFile, "\n"); // 在每个文件后面添加一个换行符，确保代码不会混在一起
                    fclose($inputFile);
                }
            } else {
                echo "File not found: " . $file . "\n";
            }
        }

        fclose($outputFile);
    }
    function onFrameworkStart(): void
    {
        $this->renderStatic();
        //渲染json
        EventManager::addListener('__json_render_msg__', function (string $event, &$data) {
            $data = ["code" => $data['code'], "msg" => $data['msg'], "data" => $data['data']];
        });
        //渲染view
        EventManager::addListener('__view_render_msg__', function (string $event, &$data) {
            $render_data = $data['data'];

            $data['tpl'] = EngineManager::getEngine()
                ->setLayout(null)
                ->setTplDir(Variables::getViewPath('error'))
                ->setEncode(false)
                ->setArray($render_data)
                ->render('error');


        });
    }

    function onRequestEnd(): void
    {

    }
}