<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\mvc;

use app\core\error\Error;
use app\core\debug\Log;
use app\core\utils\StringUtil;


/**
 * Class View
 * @package app\core\mvc
 * Date: 2020/11/30 11:42 下午
 * Author: ankio
 * Description:视图渲染
 */
class View
{
    private  $template_dir, $compile_dir, $right_delimiter, $left_delimiter;
    //      模板路径         编译路径       左边分隔符          右边分隔符
    private $template_vals = [];
    //      模板变量

    /**
     * View constructor.
     * @param string $template_dir
     * @param string $compile_dir
     * @param string $left_delimiter
     * @param string $right_delimiter
     */
    public function __construct(string $template_dir, string $compile_dir, string $left_delimiter = '<{', string $right_delimiter = '}>')
    {
        $this->left_delimiter = $left_delimiter;
        $this->right_delimiter = $right_delimiter;
        $this->template_dir = $template_dir;
        $this->compile_dir = $compile_dir;
    }


    /**
     * 编译并渲染
     * @param string $tempalte_name
     * @return false|string
     */
    public function render($tempalte_name)
    {
        $complied_file = $this->compile($tempalte_name);
       ob_start();
        $_view_obj = &$this;
        extract($this->template_vals, EXTR_SKIP);
        include $complied_file;
        return ob_get_clean();
    }


    /**
     * 模板编译
     * @param string $template_name
     * @return string
     */
    public function compile(string $template_name): string
    {
        global $__module;
        $realName=$template_name;
        $template_name = ($__module == '' ? '' : $__module . DS) . $template_name . '.tpl';
        //自动化模板名字
        $file = $this->template_dir . DS . $template_name;
        if (!file_exists($file)){
            $file2 = APP_TMP  . $realName;
            if (!file_exists($file2)){
                Error::err('错误: 文件"' . $file . '" 不存在!');
            }
            $file=$file2;
        }


        $complied_file = $this->compile_dir . DS . md5(realpath($file)) . '.' . filemtime($file) . '.' . basename($template_name) . '.php';
        if (file_exists($complied_file)) {
            return $complied_file;
        }


        $template_data = file_get_contents($file);
        $template_data = $this->_compile_struct($template_data);

        $template_data = $this->_compile_function($template_data);
        $template_data = '<?php use app\core\mvc; if(!class_exists("app\\\\core\\\\mvc\\\\View", false)) exit("模板文件禁止被直接访问.");?>' . $template_data;
        $template_data = $this->_complie_script_get($template_data);
        $template_data = $this->_complie_script_put($template_data);
        $template_data = $this->cleanRemark($template_data);
        $this->_clear_compliedfile($template_name);
        $tmp_file = $complied_file . uniqid('_tpl', true);
        if (!file_put_contents($tmp_file, $template_data))
            Error::err('错误: 写入 "' . $tmp_file . '" 文件失败.');
        $success = @rename($tmp_file, $complied_file);
        if (!$success) {
            if (is_file($complied_file)) @unlink($complied_file);
            $success = @rename($tmp_file, $complied_file);
        }
        if (!$success) Error::err('错误: 写入 "' . $complied_file . '" 文件失败.');
       return $complied_file;
    }

    /**
     * 翻译模板语法
     * @param string $template_data
     * @return string|string[]
     */
    private function _compile_struct(string $template_data)
    {
        $foreach_inner_before = '<?php if(!empty($1)){ $_foreach_$3_counter = 0; $_foreach_$3_total = count($1);?>';
        $foreach_inner_after = '<?php $_foreach_$3_index = $_foreach_$3_counter;$_foreach_$3_iteration = $_foreach_$3_counter + 1;$_foreach_$3_first = ($_foreach_$3_counter == 0);$_foreach_$3_last = ($_foreach_$3_counter == $_foreach_$3_total - 1);$_foreach_$3_counter++;?>';
        $pattern_map = [
            '<{\*([\s\S]+?)\*}>' => '<?php /* $1*/?>',
            '<{#(.*?)}>' => '<?php echo $1; ?>',
            '(<{((?!}>).)*?)(\$[\w\"\'\[\]]+?)\.(\w+)(.*?}>)' => '$1$3[\'$4\']$5',
            '(<{.*?)(\$(\w+)@(index|iteration|first|last|total))+(.*?}>)' => '$1$_foreach_$3_$4$5',
            '<{(\$[\$\w\.\"\'\[\]]+?)\snofilter\s*}>' => '<?php echo $1; ?>',
            '<{(\$[\$\w\"\'\[\]]+?)\s*=(.*?)\s*}>' => '<?php $1 =$2; ?>',
            '<{(\$[\$\w\.\"\'\[\]]+?)\s*}>' => '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>',
            '<{if\s*(.+?)}>' => '<?php if ($1) : ?>',
            '<{else\s*if\s*(.+?)}>' => '<?php elseif ($1) : ?>',
            '<{else}>' => '<?php else : ?>',
            '<{break}>' => '<?php break; ?>',
            '<{continue}>' => '<?php continue; ?>',
            '<{\/if}>' => '<?php endif; ?>',
            '<{foreach\s*(\$[\$\w\.\"\'\[\]]+?)\s*as(\s*)\$([\w\"\'\[\]]+?)}>' => $foreach_inner_before . '<?php foreach( $1 as $$3 ) : ?>' . $foreach_inner_after,
            '<{foreach\s*(\$[\$\w\.\"\'\[\]]+?)\s*as\s*(\$[\w\"\'\[\]]+?)\s*=>\s*\$([\w\"\'\[\]]+?)}>' => $foreach_inner_before . '<?php foreach( $1 as $2 => $$3 ) : ?>' . $foreach_inner_after,
            '<{\/foreach}>' => '<?php endforeach; }?>',
            '<{include\s*file=(.+?)}>' => '<?php include $_view_obj->compile($1); ?>',
        ];

        foreach ($pattern_map as $p => $r) {
            $pattern = '/' . str_replace(["<{", "}>"], [$this->left_delimiter . '\s*', '\s*' . $this->right_delimiter], $p) . '/i';
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
    private function _compile_function(string $template_data)
    {
        $pattern = '/' . $this->left_delimiter . '(\w+)\s*(.*?)' . $this->right_delimiter . '/';
        return preg_replace_callback($pattern, [$this, '_compile_function_callback'], $template_data);
    }

    /**
     * js脚本位置重定位
     * @param string $template_data
     * @return string|string[]
     */
    public function _complie_script_get(string $template_data)
    {
        $isMatched = preg_match_all('/<!--include_start-->([\s\S]*?)<!--include_end-->/', $template_data, $matches);
        if ($isMatched === 1) {
            $script = $matches[1][0];
            $fileName=md5($script)."_script.tpl";
            file_put_contents(APP_TMP.$fileName,$script);
            $template_data = str_replace($matches[0][0], '<?php $template_file_script="' . $fileName. '";?>', $template_data);
        }
        return $template_data;
    }

    /**
     * js脚本位置重定位
     * @param string $template_data
     * @return string|string[]
     */
    public function _complie_script_put(string $template_data)
    {

        $template_data = str_replace('<!--template_file_script-->', '<?php if(isset($template_file_script))include_once $_view_obj->compile($template_file_script);?>', $template_data);
        return $template_data;
    }

    /**
     * 清除注释
     * @param string $template_data
     * @return string
     */
    public function cleanRemark(string $template_data): string
    {
        $isMatched = preg_match_all('/<!--[\s\S]*?-->/', $template_data, $matches);
        if ($isMatched) {
            foreach ($matches[0] as $match){
                if(StringUtil::get($match)->startsWith("<!--["))continue;
                $template_data = str_replace($match,"",$template_data);
            }
        }
        return $template_data;
    }

    /**
     * 清除过期的文件
     * @param string $tempalte_name
     */
    private function _clear_compliedfile(string $tempalte_name)
    {
        $dir = scandir($this->compile_dir);
        if ($dir) {
            $part = md5(realpath($this->template_dir . DS . $tempalte_name));
            foreach ($dir as $d) {
                if (substr($d, 0, strlen($part)) == $part) {
                    @unlink($this->compile_dir . DS . $d);
                }
            }
        }
    }


    /**
     * 变量赋值
     * @param string|array $mixed
     * @param string $val
     */
    public function assign( $mixed, string $val = '')
    {
        if (is_array($mixed)) {
            foreach ($mixed as $k => $v) {
                if ($k != '') $this->template_vals[$k] = $v;
            }
        } else {
            if ($mixed != '') $this->template_vals[$mixed] = $val;
        }
    }

    /**
     * 函数回调
     * @param $matches
     * @return string|string[]|null
     */
    private function _compile_function_callback($matches)
    {

        //dump($matches,true);

        if (empty($matches[2])) return '<?php echo ' . $matches[1] . '();?>';

        if($matches[1]!=="unset"){
            $replace = '<?php echo ' . $matches[1] . '($1);?>';
        }else{
            $replace = '<?php  ' . $matches[1] . '($1);?>';
        }
        $sysfunc = preg_replace('/\((.*)\)\s*$/', $replace, $matches[2], -1, $count);
        if ($count) return $sysfunc;

        $pattern_inner = '/\b([-\w]+?)\s*=\s*(\$[\w"\'\]\[\-_>\$]+|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|([->\w]+))\s*?/';
        $params = "";
        if (preg_match_all($pattern_inner, $matches[2], $matches_inner, PREG_SET_ORDER)) {
            $params = "array(";
            foreach ($matches_inner as $m) $params .= '\'' . $m[1] . "'=>" . $m[2] . ", ";
            $params .= ")";
        } else {
            Error::err('错误:\'' . $matches[1] . '\' 函数的参数不正确!');
        }
        return '<?php echo ' . $matches[1] . '(' . $params . ');?>';
    }
}
