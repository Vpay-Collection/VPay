<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\mvc;



use app\core\database\Sql;

/**
 * Class Model
 * @package app\core\mvc
 * Date: 2020/12/3 12:21 上午
 * Author: ankio
 * Description: 模型类
 */
class Model extends Sql
{

    /**
     * Model constructor.
     *
     * @param string $table_name
     */
    public function __construct(string $table_name = '')
    {

        parent::__construct($table_name);
        //手动设置默认数据库位置
        $this->setDbLocation(APP_CONF,"db");
        $this->setDatabase("master");
       // $this->table($table_name);
    }


    /**
		 * 设置选项
		 * @param $idName
	 * @param $id
	 * @param $opt
	 * @param $val
		 * @return mixed
		 */
	public function setOption($idName, $id, $opt, $val)
    {
        return $this->update()->where([$idName => $id])->set([$opt => $val])->commit();
    }


}
