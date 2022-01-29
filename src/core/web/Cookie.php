<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\web;


/**
 * Class Cookie
 * @package app\core\web
 * Date: 2020/11/19 12:24 上午
 * Author: ankio
 * Description:Cookie操作类
 */
class Cookie
{
	private static ?Cookie $instance = null;
	private int $expire = 0;//过期时间 单位为s 默认是会话 关闭浏览器就不在存在
	private string $path = '';//路径 默认在本目录及子目录下有效 /表示根目录下有效
	private string $domain = '';//域
	private bool $secure = false;//是否只在https协议下设置默认不是
	private bool $httponly = false;//如果为TRUE，则只能通过HTTP协议访问cookie。 这意味着脚本语言（例如JavaScript）无法访问cookie

	/**
	 * [__construct description]
	 * 构造函数完成cookie参数初始化工作
	 * @param  array  $options  [cookie相关选项]
	 */
	private function __construct(array $options = [])
	{
	    $this->init();
		$this->getOptions($options);
	}

	private function init(){
	      $this->domain=Response::getDomain();
	      $this->httponly=true;
	      $this->path='/';
    }
	/**
		 * 获取cookie设置选项
		 * @param  array  $options  数组
	 *+----------------------------------------------------------
	 * @return void
     */
	private function getOptions(array $options = []): void
    {
		if (isset($options['expire'])) {
			$this->expire = $options['expire'];
		}
		if (isset($options['path'])) {
			$this->path = $options['path'];
		}
		if (isset($options['domain'])) {
			$this->domain = $options['domain'];
		}
		if (isset($options['secure'])) {
			$this->secure = $options['secure'];
		}
		if (isset($options['httponly'])) {
			$this->httponly = $options['httponly'];
		}

    }


	/**
		 * 获取实例
		 * @param  array  $options
		 * @return Cookie
		 */
	public static function getInstance(array $options = []): ?Cookie
    {
		if (is_null(self::$instance)) {
            self::$instance = new Cookie($options);
		}
        self::$instance->getOptions($options);
		return self::$instance;
	}


	/**
		 * 设置cookie
		 * @param         $name
	 * @param         $value
	 * @param  array  $options
		 */
	public function set($name, $value, array $options = [])
	{
		if (is_array($options) && count($options) > 0) {
			$this->getOptions($options);
		}
		if (is_array($value) || is_object($value)) {
			$value = json_encode($value);
		}
		setcookie($name, $value, $this->expire, $this->path, $this->domain,
			$this->secure, $this->httponly);
	}


	/**
		 * 获取cookie
		 * @param $name
		 * @return array|mixed
		 */
	public function get($name)
	{
		if ( ! isset($_COOKIE[$name])) {
			return null;
		}

		return $_COOKIE[$name];
	}


	/**
		 * 删除cookie
		 * @param         $name
		 */
	public function delete($name)
	{
		if ( ! isset($_COOKIE[$name])) {
			return;
		}
		$value = $_COOKIE[$name];
		setcookie($name, '', time() - 1, $this->path, $this->domain,
			$this->secure, $this->httponly);
		unset($value);
	}


    /**
     * cookie续期
     * @param int $time
     */
	public function addTime(int $time=5){
	    foreach ($_COOKIE as $name=>$value){
            setcookie($name, $value, time() + $time*60, $this->path, $this->domain,
                $this->secure, $this->httponly);
        }
    }
}