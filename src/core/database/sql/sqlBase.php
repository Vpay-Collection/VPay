<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/



namespace app\core\database\sql;
use app\core\error\AppError;
use PDO;

/**
 * Class sqlBase
 * @package app\core\database\sql
 * Date: 2020/11/20 11:32 下午
 * Author: ankio
 * Description:sql数据对象构成的基类
 */
class sqlBase
{

    protected array $opt = [];//封装常见的数据库查询选项
    protected string $tableName;
    protected ?string $traSql = null;//编译完成的sql语句
    protected array $bindParam = [];//绑定的参数列表
    protected ?sqlExec $sql = null;


	/**
* sqlBase constructor.
* @param $tableName
* @param $sqlDetail
*/
	public function __construct($tableName, $sqlDetail)
    {
        if (!class_exists("PDO") || !in_array("mysql", PDO::getAvailableDrivers(), true)) {
            new AppError("请安装PDO拓展并启用",APP_CONF."db.yml","type");
        }
        //初始化基础数据
        $this->opt['type'] = 'select';
        $this->opt['tableName'] = $tableName;
        $this->tableName = $tableName;
        $this->sql = $sqlDetail;
    }

	/**
	* 获取存储的数据选项
	* @param $head
    * @param $opt
	* @return string
	*/
	protected function getOpt($head, $opt): string
    {
        if (isset($this->opt[$opt])) return ' ' . $head . ' ' . $this->opt[$opt] . ' ';
        return ' ';
    }


	/**
	* 设置表名
	* @param string $tableName
	* @return $this
	*/
	protected function table(string $tableName)
    {
        $this->opt['tableName'] = $tableName;
        return $this;
    }


	/**
	* 设置查询条件
	* @param array $conditions 条件内容，必须是数组,格式如下["name"=>"张三","i > :hello",":hello"=>"hi"]
	* @return $this
	*/
	protected function where(array $conditions)
    {
        if (is_array($conditions) && !empty($conditions)) {
            $sql = null;
            $join = [];
            reset($conditions);

            foreach ($conditions as $key => $condition) {
                if (is_int($key)) {
                    $join[] = $condition;
                    unset($conditions[$key]);
                    continue;
                }
                $key = str_replace('.', '_', $key);
                if (substr($key, 0, 1) != ":") {
                    unset($conditions[$key]);
                    $conditions[":_WHERE_" . $key] = $condition;
                    $join[] = "`" . str_replace('.', '`.`', $key) . "` = :_WHERE_" . $key;
                }

            }
            if (!$sql) $sql = join(" AND ", $join);

            $this->opt['where'] = $sql;
            $this->bindParam += $conditions;
        }
        return $this;
    }

}
