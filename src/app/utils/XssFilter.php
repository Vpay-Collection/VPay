<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\utils
 * Class XssFilter
 * Created By ankio.
 * Date : 2023/7/20
 * Time : 12:41
 * Description :
 */

namespace app\utils;



use DOMDocument;

class XssFilter {
    private DOMDocument $dom;
    private string $html;
    private bool|DOMDocument $m_ok;
    private array $allow_attrs = array('title', 'src', 'href', 'id', 'class', 'style', 'width', 'height', 'alt', 'target', 'align');
    private array $allow_tags = array('a', 'img', 'br', 'strong', 'b', 'code', 'pre', 'p', 'div', 'em', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'ul', 'ol', 'tr', 'th', 'td', 'hr', 'li', 'u');
    /**
     * 构造函数
     *
     * @param string $html 待过滤的文本
     * @param string $charset 文本编码，默认utf-8
     * @param array $tag 允许的标签，如果不清楚请保持默认，默认已涵盖大部分功能，不要增加危险标签
     */
    public function __construct(string $html, string $charset = 'utf-8', array $tag = []){
        if(!empty($tag)){
            $this->allow_tags =  $tag;
        }
        //先过滤标签
        $this->html = strip_tags($html, '<' . implode('><', $this->allow_tags) . '>');
        if (empty($this->html)) {
            return ;
        }
        $this->html = "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$charset}\">" . $this->html;
        $this->dom = new DOMDocument();
        $this->dom->strictErrorChecking = false;
        libxml_use_internal_errors(false);
        if(!$this->dom->loadHTML($this->html)){
            $this->html = "";//加载失败说明存在畸形
        }
    }

    /**
     * 获得过滤后的内容
     */
    public function getHtml(): string
    {
        if (empty($this->html)) {
            return '';
        }
        $nodeList = $this->dom->getElementsByTagName('*');
        for ($i = 0; $i < $nodeList->length; $i++){
            $node = $nodeList->item($i);
            if (in_array($node->nodeName, $this->allow_tags)) {
                $method = "__node_{$node->nodeName}";
                $this->__common_attr($node);
                if (method_exists($this, $method)) {
                    $this->$method($node);
                }
            }
        }
        return strip_tags($this->dom->saveHTML(), '<' . implode('><', $this->allow_tags) . '>');
    }
    //过滤http
    private function __true_url($url){
        if (preg_match('#^https?://.+#is', $url)) {
            return $url;
        }else{
            return 'http://' . $url;
        }
    }
    //样式过滤
    private function __get_style($node): array|string|null
    {
        if ($node->attributes->getNamedItem('style')) {
            $style = $node->attributes->getNamedItem('style')->nodeValue;
            $style = str_replace('\\', ' ', $style);
            $style = str_replace(array('&#', '/*', '*/'), ' ', $style);
            return preg_replace('#e.*x.*p.*r.*e.*s.*s.*i.*o.*n#Uis', ' ', $style);
        }else{
            return '';
        }
    }
    //链接
    private function __get_link($node, $att){
        $link = $node->attributes->getNamedItem($att);
        if ($link) {
            return $this->__true_url($link->nodeValue);
        }else{
            return '';
        }
    }

    private function __setAttr($dom, $attr, $val): void
    {
        if (!empty($val)) {
            $dom->setAttribute($attr, $val);
        }
    }

    private function __set_default_attr($node, $attr, $default = ''): void
    {
        $o = $node->attributes->getNamedItem($attr);
        if ($o) {
            $this->__setAttr($node, $attr, $o->nodeValue);
        }else{
            $this->__setAttr($node, $attr, $default);
        }
    }

    private function __common_attr($node): void
    {
        $list = array();
        foreach ($node->attributes as $attr) {
            if (!in_array($attr->nodeName,
                $this->allow_attrs)) {
                $list[] = $attr->nodeName;
            }
        }
        foreach ($list as $attr) {
            $node->removeAttribute($attr);
        }
        $style = $this->__get_style($node);
        $this->__setAttr($node, 'style', $style);
        $this->__set_default_attr($node, 'title');
        $this->__set_default_attr($node, 'id');
        $this->__set_default_attr($node, 'class');
    }

    private function __node_img($node): void
    {


        $this->__set_default_attr($node, 'src');
        $this->__set_default_attr($node, 'width');
        $this->__set_default_attr($node, 'height');
        $this->__set_default_attr($node, 'alt');
        $this->__set_default_attr($node, 'align');

    }

    private function __node_a($node): void
    {

        $href = $this->__get_link($node, 'href');

        $this->__setAttr($node, 'href', $href);
        $this->__set_default_attr($node, 'target', '_blank');
    }

    private function __node_embed($node): void
    {

        $link = $this->__get_link($node, 'src');
        $this->__setAttr($node, 'src', $link);
        $this->__setAttr($node, 'allowscriptaccess', 'never');
        $this->__set_default_attr($node, 'width');
        $this->__set_default_attr($node, 'height');
    }

}
