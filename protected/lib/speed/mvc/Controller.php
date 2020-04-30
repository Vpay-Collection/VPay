<?php

namespace app\lib\speed\mvc;


class Controller
{
    public $layout;
    public $_auto_display = true;
    protected $_v;
    private $_data = array();

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
    }

    public function &__get($name)
    {
        return $this->_data[$name];
    }

    function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * @param string $msg 跳转之前显示的信息
     * @param string $url 跳转的url
     */
    public function tips($msg, $url)
    {
        $url = "location.href=\"{$url}\";";
        echo "<html lang=''><head><title></title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
        exit;
    }

    /**
     * @param string $url 跳转的url
     * @param int $delay 延时时间
     */
    public function jump($url, $delay = 0)
    {
        echo "<html lang=''><head><title></title><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
        exit;
    }

    /**
     * @param null $tpl_name 模板名称
     * @param bool $return 是否直接返回
     * @return false|string
     */
    public function display($tpl_name, $return = false)
    {

        if (!$this->_v) {
            $compile_dir = isset($GLOBALS['view']['compile_dir']) ? $GLOBALS['view']['compile_dir'] : APP_DIR . DS . 'protected' . DS . 'tmp';
            $this->_v = new View(APP_DIR . DS . 'protected' . DS . 'view', $compile_dir);
        }
        $this->_v->assign(get_object_vars($this));
        $this->_v->assign($this->_data);
        if ($this->layout) {
            $this->_v->assign('__template_file', $tpl_name);
            $tpl_name = $this->layout;
        }
        $this->_auto_display = false;

        if ($return) {
            return $this->_v->render($tpl_name);
        } else {
            echo $this->_v->render($tpl_name);
            return '';
        }
    }
}