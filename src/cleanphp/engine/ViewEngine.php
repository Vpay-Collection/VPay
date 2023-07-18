<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\engine;


use cleanphp\App;
use cleanphp\base\DumpJson;
use cleanphp\base\Error;
use cleanphp\base\EventManager;
use cleanphp\base\Json;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Route;
use cleanphp\base\Variables;
use cleanphp\file\File;
use cleanphp\file\Log;

/**
 * Class View
 * @package cleanphp\mvc
 * Date: 2020/11/30 11:42 下午
 * Author: ankio
 * Description:视图渲染
 */
class ViewEngine extends BaseEngine
{

    private ?string $__layout = "";
    private bool $__encode = true;
    private array $__data = [];
    private string $__left_delimiter = "{";
    private string $__right_delimiter = "}";
    private string $__compile_dir;
    private string $__template_dir;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->__compile_dir = Variables::getStoragePath("view");
        $this->__template_dir = Variables::getViewPath();
        File::mkDir($this->__compile_dir);
    }



    /**
     * 设置layout文件
     * @param ?string $file layout文件名
     * @return ViewEngine
     */
    public function setLayout(?string $file): ViewEngine
    {
        $this->__layout = $file;
        return $this;
    }

    /**
     * 是否输出进行html编码
     * @return bool
     */
    public function isEncode(): bool
    {
        return $this->__encode === true;
    }

    /**
     * 是否输出进行html编码
     * @param bool $encode 是否编码
     * @return ViewEngine
     */
    public function setEncode(bool $encode): ViewEngine
    {
        $this->__encode = $encode;
        return $this;
    }

    /**
     * 设置模板数据数组
     * @param array $array 模板数据数组
     * @return ViewEngine
     */
    function setArray(array $array): ViewEngine
    {
        $this->__data = array_merge($this->__data, $array);
        return $this;
    }


    function renderMsg(int $code = 404, string $title = "", $msg = "", int $time = -1, string $url = '/', string $desc = "立即跳转"): string
    {
        parent::renderMsg($code, $title, $msg, $time, $url, $desc);
        $__total_time = round((microtime(true) - Variables::get("__frame_start__", 0)) * 1000, 4);
        $add = App::$debug?$this->getDebugData($__total_time):"";
        $array = [
            "data" => ["code" => $code, "title" => $title, "msg" => $msg, "time" => $time, "url" => $url, "desc" => $desc],
            "tpl" => <<<TPL

$add
<!DOCTYPE html>

<html  lang="zh-cn">
<head>
 <meta name="viewport" content="width=device-width, initial-scale=1">
   
    <title>{$title}</title>
    
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 100px;
        }

        h1 {
            color: #333333;
            font-size: 32px;
        }

        p {
            color: #666666;
            font-size: 25px;
            line-height: 1.6;
               word-break: break-all;
        }
         a {
        color: #2E5CD5;
        cursor: pointer;
        text-decoration: none
    }

    a:hover {
        text-decoration: underline;
    }
 
    </style>
</head>
<body>
    <h1>$title</h1>
    <p>{$msg}</p>
      <span id="jump_box" style="font-size:25px;"></span>
    <script>
    let wait = "{$time}";
    if (parseInt(wait) !== -1) {
        document.getElementById('jump_box').innerHTML = "还有<span id='jump'>{$time}</span>秒为您自动跳转，<a href='{$url}' target='_self'>{$desc}</a>"
        setInterval(function () {
            document.getElementById("jump").innerText = (--wait).toString();
            if (wait <= 0) {
                location.href = "{$url}";
            }
        }, 1000);
    } else if ("{$url}" !== "") {
        document.getElementById('jump_box').innerHTML = "<span id='jump'><a href='{$url}' target='_self'>{$desc}</a></span>"
    }
</script>
</body>
</html>


TPL
        ];
        EventManager::trigger("__view_render_msg__", $array, true);
        return $array["tpl"];
    }


    public function onControllerError($controller, $method): ?string
    {
        $__module = Variables::get("__request_module__", '');
        //构建模板
        $tpl_name = $controller . '_' . $method;
        $tpl = Variables::getViewPath($__module, $tpl_name . ".tpl");
        //模板存在，使用模板渲染
        if (file_exists($tpl)) {
            return $this->setEncode(false)->render($tpl_name);
        }
        return null;
    }


    private function getDebugData($__total_time): string
    {



            $memory = round((memory_get_usage() - $GLOBALS['__memory_start__'])/ 1024 / 1024,2);
            $headers = array_merge([$_SERVER["REQUEST_METHOD"] . " " . $_SERVER["REQUEST_URI"]], Request::getHeaders());

        $__headers = addslashes(Json::encode((new DumpJson())->dumpType($headers)));
        $__log = addslashes(Json::encode(Log::getInstance("ViewEngine")->getTempLog()));

        $__version = Variables::getVersion();
        $__dumps = addslashes(Json::encode((new DumpJson())->dumpType($GLOBALS)));

        $__dumps2 = addslashes(Json::encode((new DumpJson())->dumpType($this->__data)));

        $__path =  $_SERVER['REQUEST_URI'];

        return <<<EOF
<script>
(function() {
  var log = {
      info(msg, title) {
            this.pretty(title, msg, 'primary');
        },
        error(msg, title) {
            this.pretty(title, msg, 'danger');
        },
        success(msg, title) {
            this.pretty(title, msg, 'success');
        },
        warning(msg, title) {
            this.pretty(title, msg, 'warning');
        },
        typeColor(type = 'default') {
            let color = '';
            switch (type) {
                case 'primary':
                    color = '#2d8cf0';
                    break;
                case 'success':
                    color = '#19be6b';
                    break;
                case 'info':
                    color = '#909399';
                    break;
                case 'warning':
                    color = '#ff9900';
                    break;
                case 'danger':
                    color = '#f03f14';
                    break;
                default:
                    color = '#35495E';
                    break;
            }
            return color;
        },
        print(text, type, back) {
            back = back || false;
            if (typeof text === 'object') { // 如果是对象则调用打印对象方式
                console.dir(text);
                return;
            }
            if (back) { // 如果是打印带背景图的
                console.log(
                    "%c "+text,
                    "background:"+this.typeColor(type)+"; padding: 2px; border-radius: 0px;color: #fff;"
                );
            } else {
                console.log(
                    "%c "+text,
                    "color: "+this.typeColor(type)+";"
                );
            }
        },
        pretty(title, text, type) {
            title = title || "CleanPHP";
            if (typeof text === 'object') { // 如果是对象则调用打印对象方式
                this.print(title, type, true);
                console.log(text);
                return;
            }
            console.log(
                "%c "+title+" %c "+text+" %c",
                "background:"+this.typeColor(type)+";border:0px solid "+this.typeColor(type)+"; padding: 1px; border-radius: 4px 0 0 4px; color: #fff;",
                "border:0px solid "+this.typeColor(type)+"; padding: 1px; border-radius: 0 4px 4px 0; color: "+this.typeColor(type)+";",
                "background:transparent"
            );
        },
  };
   log.success("----------------{$__path}-------------------","页面加载");
  var page_start_time = new Date().getTime();
  log.info("$__total_time ms","运行时间");
   log.info("$memory MB","占用内存");
  log.info("$__version","Cleanphp版本");
  window.onload = function (){
    log.info((Math.round(new Date().getTime()  - page_start_time))+" ms","资源加载时间");
    log.success("-----------------{$__path}------------------","页面加载");
  }
  log.warning("-------------页面日志--------------");
  function outputArray(array) {
    array = JSON.parse(array);
    for(var i = 0; i < array.length; i++) {
      var log_ = array[i];
      if(typeof log_ === "string"){
          log_ = log_.trim();
           if(log_.indexOf("WARN")>0){
        log.warning(log_);
    }else if(log_.indexOf("ERROR")>0){
        log.error(log_);
    }else{
        log.info(log_);
    }
      }
   
  }
  }
  
  outputArray('$__log');
  log.warning("-------------请求头--------------");
  log.info(JSON.parse('$__headers'));
  log.warning("-------------全局变量--------------");
 
  log.info(JSON.parse('$__dumps'));
  log.warning("-------------页面变量--------------");

  log.info(JSON.parse('$__dumps2'));
  
})();
</script>

EOF;
    }

    function render(...$data): string
    {
        App::$debug && Variables::set("__view_time_start__", microtime(true));

        $template_name = $data[0];
        [$file, $template_name] = $this->preCompileLayout($template_name);

        $complied_file = $this->compile($template_name, $file);
        $__total_time = round((microtime(true) - Variables::get("__frame_start__", 0)) * 1000, 4);
        ob_start();
        if (App::$debug) {
            echo $this->getDebugData($__total_time);
        }
        extract($this->__data);

        include $complied_file;

        App::$debug && Log::record("ViewEngine", sprintf("编译运行时间：%s 毫秒", $__total_time), Log::TYPE_WARNING);

        $result = ob_get_clean();

        EventManager::trigger("__on_view_render__",$result);

        return $result;
    }

    /**
     * 预编译layout
     * @param string $template_name
     * @return array
     */
    private function preCompileLayout(string $template_name): array
    {
        if (!empty($this->__layout)) {
            if ($template_name === $this->__layout){
                Error::err("父模板不能与当前模板一致，会导致死循环。", [], "ViewEngine");
            }
            $file = $this->checkTplFile($this->__layout);//检查模板文件是否存在
            $view = $this->checkTplFile($template_name);//检查视图文件是否存在
            $this->setData("__template_file", $template_name);

            return [$file, $this->__layout];
        }
        return [$this->checkTplFile($template_name), $template_name];
    }

    /**
     * 检测tpl文件是否存在
     */
    private function checkTplFile(string $template_name): string
    {

        $__module = Variables::get("__request_module__", "");

        $tpl_names = [
            $this->__template_dir . DS . $__module . DS . $template_name . '.tpl',
            $this->__template_dir . DS . $template_name . '.tpl',
            Variables::getViewPath($template_name . '.tpl'),
            Variables::getStoragePath('view', $template_name . '.tpl')
        ];
        $file = null;
        foreach ($tpl_names as $tpl_name) {
            if (file_exists($tpl_name)) {
                $file = $tpl_name;
                break;
            }
        }
        if ($file === null) {
            $error = '';
            foreach ($tpl_names as $tpl_name)
                $error .= sprintf("模板文件（%s）不存在 \n ", $tpl_name);
            Error::err($error, [], "ViewEngine");
        }

        return $file;
    }

    /**
     * 设置模板数据
     * @param string $key
     * @param $value
     * @return ViewEngine
     */
    function setData(string $key, $value): ViewEngine
    {
        $this->__data[$key] = $value;
        return $this;
    }

    /**
     * 清除过期的文件
     * @param string $hash
     */
    private function _clear_complied_file(string $hash): void
    {
        $dir = scandir($this->__compile_dir);
        if ($dir) {
            foreach ($dir as $d) {
                if (substr($d, 0, 8) == $hash) {
                    @unlink($this->__compile_dir . DS . $d);
                }
            }
        }
    }

    /**
     * 模板编译
     * @param string $template_name 模板文件名
     * @param string|null $file
     * @return string
     */
    private function compile(string $template_name, string $file = null): string
    {

        if ($file == null) $file = $this->checkTplFile($template_name);
        $hash = substr(md5(realpath($file)), 8, 8);
        $file_hash = substr(md5_file($file), 8, 8);
        $complied_file = $this->__compile_dir . DS . $hash . '.' . $file_hash . "." . basename($template_name) . '.php';

        if (!App::$debug && file_exists($complied_file)) {//调试模式下，直接重新编译
            return $complied_file;
        }

        $template_data = file_get_contents($file);
        $template_data = $this->_compile_struct($template_data);
        $template_data = $this->_compile_function($template_data);


        $template_data = '<?php use cleanphp\engine; if(!class_exists("' . str_replace("\\", "\\\\", ViewEngine::class) . '", false)) exit("[ CleanPHP ] 渲染错误：模板文件禁止被直接访问.");?>' . $template_data;

        $template_data = $this->_clean_remark($template_data);
        $this->_clear_complied_file($hash);
        if (!file_put_contents($complied_file, $template_data)){
            Error::err(sprintf("写入 %s 文件失败", $complied_file), [], "ViewEngine");
        }
        return $complied_file;
    }

    /**
     * 翻译模板语法
     * @param string $template_data
     * @return string|string[]
     */
    private function _compile_struct(string $template_data): array|string
    {
        $foreach_inner_before = '<?php if(!empty($1)){ $_foreach_$3_counter = 0; $_foreach_$3_total = count($1);?>';
        $foreach_inner_after = '<?php $_foreach_$3_index = $_foreach_$3_counter;$_foreach_$3_iteration = $_foreach_$3_counter + 1;$_foreach_$3_first = ($_foreach_$3_counter == 0);$_foreach_$3_last = ($_foreach_$3_counter == $_foreach_$3_total - 1);$_foreach_$3_counter++;?>';
        $pattern_map = [
            '{\*([\s\S]+?)\*}' => '<?php /* $1*/?>',
            '{#(.*?)}' => '<?php echo $1; ?>',

            '({((?!}).)*?)(\$[\w\"\'\[\]]+?)\.(\w+)(.*?})' => '$1$3[\'$4\']$5',
            '({.*?)(\$(\w+)@(index|iteration|first|last|total))+(.*?})' => '$1$_foreach_$3_$4$5',
            '{(\$[\$\w\.\"\'\[\]]+?)\snofilter\s*}' => '<?php echo $1; ?>',
            '{([\w\$\.\[\]\=\'"\s]+)\?(.*?:.*?)}' => '<?php echo $1?$2; ?>',

            '{(\$[\$\w\"\'\[\]]+?)\s*=(.*?)\s*}' => '<?php $1=$2; ?>',
            '{(\$[\$\w\.\"\'\[\]]+?)\s*}' => '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>',

            '{while\s*(.+?)}' => '<?php while ($1) : ?>',
            '{\/while}' => '<?php endwhile; ?>',

            '{if\s*(.+?)}' => '<?php if ($1) : ?>',

            '{else\s*if\s*(.+?)}' => '<?php elseif ($1) : ?>',
            '{else}' => '<?php else : ?>',
            '{break}' => '<?php break; ?>',
            '{continue}' => '<?php continue; ?>',

            '{\/if}' => '<?php endif; ?>',
            '{foreach\s*(\$[\$\w\.\"\'\[\]]+?)\s*as(\s*)\$([\w\"\'\[\]]+?)}' => $foreach_inner_before . '<?php foreach( $1 as $$3 ) : ?>' . $foreach_inner_after,
            '{foreach\s*(\$[\$\w\.\"\'\[\]_]+?)\s*as\s*(\$[\w\"\'\[\]]+?)\s*=>\s*\$([\w\"\'\[\]]+?)}' => $foreach_inner_before . '<?php foreach( $1 as $2 => $$3 ) : ?>' . $foreach_inner_after,
            '{\/foreach}' => '<?php endforeach; }?>',

            '{include\s*file=(.+?)}' => '<?php include $this->compile($1); ?>',
        ];

        foreach ($pattern_map as $p => $r) {
            $pattern = '/' . str_replace(["{", "}"], [$this->__left_delimiter . '\s*', '\s*' . $this->__right_delimiter], $p) . '/i';
            $count = 1;
            while ($count != 0) {
                $template_data = preg_replace($pattern, $r, $template_data, -1, $count);
            }
        }

        return $template_data;
    }

    /**
     * 函数编译
     * @param string $template_data
     * @return string|string[]|null
     */
    private function _compile_function(string $template_data): array|string|null
    {
        $pattern = '/' . $this->__left_delimiter . '(\w+)\s*(.*?)' . $this->__right_delimiter . '/';
        return preg_replace_callback($pattern, [$this, '_compile_function_callback'], $template_data);
    }

    /**
     * 清除html注释
     * @param string $template_data
     * @return string
     */
    private function _clean_remark(string $template_data): string
    {
        return Route::replaceStatic($template_data);
    }

    function getContentType(): string
    {
        return 'text/html';
    }

    function renderError(string $msg, array $traces, string $dumps, string $tag): string
    {
        Variables::set("__request_module__", "");
        $tpl = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{$msg nofilter}</title>
    <style type="">
    body {
	padding: 0;
	margin: 0;
	word-wrap: break-word;
	word-break: break-all;
	font-family: Courier,Arial,sans-serif;
	background: #ebf8ff;
	color: #5e5e5e
}

div,h2,p,span {
	margin: 0;
	padding: 0
}

ul {
	margin: 0;
	padding: 0;
	list-style-type: none;
	font-size: 0;
	line-height: 0
}

#body {
	
	margin: 0 auto
}

#main {
	width: 100%;
	max-width: 900px;
	margin: 13px auto 0 auto;
	padding: 0 0 35px 0
}

#contents {
	
	margin: 13px auto 0 auto;
	background: #FFF;
	padding: 10px
}

#contents h2 {
	display: block;
	background: #cff0f3;
	font: bold:20px;
	padding: 12px 30px;
	margin: 0 10px 22px 1px
}

#contents ul {
	padding: 0 18px 0 18px;
	font-size: 0;
	line-height: 0
}

#contents ul li {
	display: block;
	    padding: 10px 0 0;
	color: #8f8f8f;
	background-color: inherit;
	font: normal 14px Arial,Helvetica,sans-serif;
	margin: 0
}

#contents ul li span {
	display: block;
	color: #408baa;
	background-color: inherit;
	font: bold 14px Arial,Helvetica,sans-serif;
	padding: 0 0 10px 0;
	margin: 0
}

#oneborder {
	font: normal 14px Arial,Helvetica,sans-serif;
	border: #ebf3f5 solid 4px;
	margin: 0 18px;
	padding: 10px 20px;
	line-height: 23px;
	overflow:scroll;
	white-space:nowrap;
}

#oneborder span {
	padding: 0;
	margin: 0
}

#oneborder #current {
	background: #cff0f3
}

pre {
	white-space: pre-wrap
}
    </style>
</head>
<body>
<div id="main">
    <div id="contents">
    {if $dump!==""}
     <h2><pre>错误发生前已输出的内容：<br>{$dump}</pre></h2>
    {/if}
        
        <h2>
            <pre>{$msg nofilter}</pre>
        </h2>
        {foreach $array as $key => $trace}
            <ul>
                <li><span>
                {$trace["title"]}</span>  
                <span style="color: #094e5a">
                {$trace["func"]}</span>  
                </li>
            </ul>
            <div id="oneborder">
               
                {foreach $trace["data"] as $singleLine}
                    {$singleLine nofilter}
                {/foreach}
</div>
          
        {/foreach}
    </div>
</div>
<div style="clear:both;padding-bottom:50px;"></div>
</body>
</html>';
        $file = Variables::getCachePath("temp_error.tpl");
        if (!is_dir(Variables::getCachePath())) File::mkDir(Variables::getCachePath());

        if (!file_exists($file) || App::$debug) file_put_contents($file, $tpl);

        $this->setTplDir(Variables::getCachePath());
        $this->__layout = '';
        $setArray = [];
        foreach ($traces as $key => $trace) {
            if (is_array($trace) && !empty($trace["file"])) {
                $trace["keyword"] = $trace["keyword"] ?? "";
                $sourceLine = self::errorFile($trace["file"], $trace["line"], $trace["keyword"]);
                $trace["line"] = $sourceLine["line"];
                unset($sourceLine["line"]);
                if ($sourceLine) {
                    $setArray[] = [
                        "title" => sprintf("#%s %s(%s)", $key, $trace['file'], $trace['line']),
                        "func" => sprintf("%s%s%s", $trace["class"] ?? "", $trace["type"] ?? "", $trace['function'] ?? ""),
                        "line" => $trace["line"],
                        "data" => $sourceLine
                    ];
                }
            }
        }
        $this->__data = ["msg" => $msg, "dump" => $dumps, "array" => $setArray];
        return $this->render("temp_error");
    }

    /**
     * 设置模板目录
     * @param string $dir
     * @return ViewEngine
     */
    public function setTplDir(string $dir): ViewEngine
    {
        $this->__template_dir = $dir;
        return $this;
    }

    /**
     * @param string $file 错误文件名
     * @param int $line 错误文件行,若为-1则指定msg查找
     * @param string $msg 当line为-1才有效
     * @return array
     */
    public static function errorFile(string $file, int $line = -1, string $msg = ""): array
    {
        if (!(file_exists($file) && is_file($file))) {
            return [];
        }
        $data = file($file);
        $count = count($data) - 1;
        $returns = [];
        if ($line == -1) {
            //查找文本
            for ($i = 0; $i <= $count; $i++) {
                if (str_contains($data[$i], $msg)) {
                    $line = $i + 1;
                    break;
                }
            }
        }
        $returns["line"] = $line;
        $start = $line - 5;
        if ($start < 1) {
            $start = 1;
        }
        $end = $line + 5;
        if ($end > $count) {
            $end = $count + 1;
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $line) {
                $returns[] = "<div id='current'>" . $i . ".&nbsp;" . self::highlightCode($data[$i - 1]) . "</div>";
            } else {
                $returns[] = $i . ".&nbsp;" . self::highlightCode($data[$i - 1]);
            }
        }
        return $returns;
    }

    /**
     * 高亮代码
     * @param string $code
     * @return bool|string|string[]
     */
    private static function highlightCode(string $code): array|bool|string
    {
        $code = preg_replace('/(\/\*\*)/', '///**', $code);
        $code = preg_replace('/(\s\*)[^\/]/', '//*', $code);
        $code = preg_replace('/(\*\/)/', '//*/', $code);
        if (preg_match('/<\?(php)?[^[:graph:]]/i', $code)) {
            $return = highlight_string($code, true);
        } else {
            $return = preg_replace('/(&lt;\?php&nbsp;)+/i', "",
                highlight_string("<?php " . $code, true));
        }
        return str_replace(['//*/', '///**', '//*'], ['*/', '/**', '*'], $return);
    }

    /**
     * 函数回调
     * @param $matches
     * @return string|string[]|null
     */
    private function _compile_function_callback($matches): array|string|null
    {

        if (empty($matches[2])) return '<?php echo ' . $matches[1] . '();?>';

        if ($matches[1] !== "unset") {
            $replace = '<?php echo ' . $matches[1] . '($1);?>';
        } else {
            $replace = '<?php  ' . $matches[1] . '($1);?>';
        }
        $sync = preg_replace('/\((.*)\)\s*$/', $replace, $matches[2], -1, $count);
        if ($count) return $sync;

        $pattern_inner = '/\b([-\w]+?)\s*=\s*(\$[\w"\'\]\[\-_>\$]+|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|([->\w]+))\s*?/';
        if (preg_match_all($pattern_inner, $matches[2], $matches_inner, PREG_SET_ORDER)) {
            $params = "array(";
            foreach ($matches_inner as $m) $params .= '\'' . $m[1] . "'=>" . $m[2] . ", ";
            $params .= ")";
            return '<?php echo ' . $matches[1] . '(' . $params . ');?>';
        }
        return "";
    }

    public function onNotFound($msg = ""): void
    {
      //  dumps($msg);
        $__module = Variables::get("__request_module__", '');
        $__controller = Variables::get("__request_controller__", '');
        $__action = Variables::get("__request_action__", '');
        $base = 'app\\' . Variables::getSite("\\") . 'controller\\' . $__module . '\\' . "BaseController";
        $controller = 'app\\' . Variables::getSite("\\") . 'controller\\' . $__module . '\\' . ucfirst($__controller);
        if (class_exists($controller)) {
            if(method_exists($controller,$__action)){
                (new $controller())->$__action();
            }else{
                new $controller();
            }
        } elseif (class_exists($base)) {
            new $base();
        }

        //调试模式才显示详细错误
        $result = $this->onControllerError($__controller, $__action);
        if ($result === null) {
            $result = $this->renderMsg( 404, "404 Not Found",  App::$debug?$msg:"资源不存在，请重试。");
        }

        (new Response())
            ->code(200)
            ->contentType($this->getContentType())
            ->render($result)
            ->send();

    }
}
