<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\ankioTask\core;


use app\core\debug\Debug;
use app\core\debug\Log;
use app\core\mvc\Model;
use app\core\web\Response;
use app\lib\Async\Async;

/**
 * Class Server
 * @package app\extend\net_ankio_tasker\core
 * Date: 2020/12/31 09:57
 * Author: ankio
 * Description:Tasker服务
 */
class Server extends Model
{

    private static ?Server $instance=null;

    private string $taskerUrl;

    public function __construct()
    {
        parent::__construct("extend_lock");
        $this->setDbLocation(EXTEND_TASKER."data".DS, "db");
        $this->setDatabase("sqlite");
        $this->execute(
        "CREATE TABLE  IF NOT EXISTS extend_lock(
              ock_time varchar(200)
            )"
    );
        $this->taskerUrl=Response::getAddress()."/tasker_server/";
        //任务URL
    }

    /**
     * 获取对象实例
     * @return Server
     */
    public static function getInstance(): ?Server
    {
        return self::$instance===null?(self::$instance=new Server()):self::$instance;
    }

    /**
     * 定时任务路由，用于对定时任务进行路由
     * @return void
     */
    public  function route()
    {
        $split=explode("/",$_SERVER['REQUEST_URI']);
        if(sizeof($split)!==3)return;
        if($split[1]!=="tasker_server")return;
        Async::getInstance()->response();
        switch ($split[2]){
            case "init":$this->init();break;
        }

    }

    /**
     * 启动任务扫描服务
     * @return void
     */
    public function start(){
        if(!$this->isLock()){//没有锁定，请求保持锁定
            Async::getInstance()->request($this->taskerUrl."init","GET",[],[],"tasker_start");
        }
    }

    /**
     *  停止服务
     * @return void
     */
    public function stop(){
        self::getInstance()->emptyTable("extend_lock");
    }


    /**
     *  服务启动与初始化
     * @return void
     */
    private function init()
    {
     //   $this->stop();
        do {
            Debug::i("task","10s pass....");
            $this->lock(time());//更新锁定时间
            //循环扫描
            Tasker::getInstance()->run();
            sleep(10);
            if($this->isStop()){//间歇10秒后如果发现停止
                break;
            }
        } while(true);

        exitApp("服务退出，框架退出");
    }

    /**
     * 更新锁定时间
     * @param $time int 锁定时间
     * @return void
     */
    private function lock(int $time){

        $data=self::getInstance()->select()->table("extend_lock")->limit(1)->commit();
        if(empty($data)){
            self::getInstance()->insert(SQL_INSERT_NORMAL)->keyValue(["lock_time"=>$time])->table("extend_lock")->commit();
        }else self::getInstance()->update()->set(["lock_time"=>$time])->table("extend_lock")->commit();
    }

    /**
     *  判断是否停止
     * @return bool
     */
    private function isStop(): bool
    {
        $data=self::getInstance()->select()->table("extend_lock")->limit(1)->commit();
        if(empty($data))return false;
        return (time()-intval($data[0]['lock_time'])>20);
    }


    /**
     *  判断是否锁定
     * @return bool
     */
    private function isLock(): bool
    {
        $data=self::getInstance()->select()->table("extend_lock")->limit(1)->commit();

        if(empty($data))return false;
        return (time()-intval($data[0]['lock_time'])<15);
    }
}