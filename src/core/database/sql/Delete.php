<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/



namespace app\core\database\sql;


/**
 * Class Delete
 * @package app\core\database\sql
 * Date: 2020/11/22 10:51 下午
 * Author: ankio
 * Description:删除封装
 */
class Delete extends sqlBase
{
	/**
	* 初始化
	* @return $this
	*/
	public function delete(): Delete
    {
        $this->opt = [];
        $this->opt['tableName'] = $this->tableName;
        $this->opt['type'] = 'delete';
        $this->bindParam = [];
        return $this;
    }

	/**
	* 设置表
	* @param string $tableName 表名
	* @return Delete
	*/
	public function table(string $tableName): Delete
    {
        return parent::table($tableName);
    }

	/**
	* 设置条件
	* @param array $conditions 条件内容，必须是数组,格式如下["name"=>"张三","i > :hello",":hello"=>"hi"]
	* @return Delete
	*/
	public function where(array $conditions): Delete
    {
        return parent::where($conditions);
    }

	/**
     * 提交
     * @return mixed
     */
	public function commit()
    {
        $this->translateSql();
        return $this->sql->execute($this->traSql, $this->bindParam, false);
    }

	/**
     * 编译
     */
	private function translateSql()
    {
        $sql = $this->getOpt('DELETE FROM', 'tableName');
        $sql .= $this->getOpt('WHERE', 'where');
        $this->traSql = $sql . ";";
    }
}
