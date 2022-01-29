<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/


namespace app\core\database\sql;

use app\core\config\Config;
use app\core\error\AppError;
use app\core\error\SqlCheckError;
use app\core\debug\Log;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class sqlExec
 * @package app\core\database\sql
 * Date: 2020/11/20 11:35 下午
 * Author: ankio
 * Description:数据库执行基类
 */
class sqlExec
{


    public string $sqlIndex = "master";
    protected string $sqlType = "mysql";
	private array $sqlList = [];
	private string $db = "";
	private string $name = "";
	private array $dbData = [];

	private static array $instances=[];

	/**
	* 设置数据库信息存储文件
	* @param string $file 选择数据库的文件
    * @param string $name 选择数据库文件中的数据库
	*/
	public function setDbFile(string $file, string $name)
    {
        $this->db = $file;
        $this->name = $name;
        $this->getDbFile();
    }

	/**
	* 获取数据库信息
	* @return mixed|null
	*/
	private function getDbFile()
    {
        if ($this->db !== null && $this->name !== null) {
            $this->dbData = Config::getInstance($this->name)->setLocation($this->db)->get();
        } else {
            $this->dbData = Config::getInstance("db")->get();
        }
        return $this->dbData;

    }

	/**
	* 设置数据库
	* @param $sqlIndex
	* @return $this
	*/
	public function setDatabase($sqlIndex): sqlExec
    {
        $this->getDbFile();
        $this->sqlIndex = $sqlIndex;
        $this->sqlType = isset($this->getDbData()[$sqlIndex]['type']) ? $this->getDbData()[$sqlIndex]['type'] : "mysql";
        return $this;
    }

	/**
	* 获取数据库数据
	* @return mixed|null
	*/
	public function getDbData()
    {
        if ($this->dbData === null) {
            return $this->getDbFile();
        } else return $this->dbData;
    }

    /**
     * 清空数据表
     * @param $string
     * @return array|int
     */
	public function emptyTable($string)
    {
        switch ($this->sqlType) {
            case "sqlite2":
            case "sqlite3":
                return $this->execute("DELETE FROM '$string';");
            case "mysql":
                return $this->execute("TRUNCATE TABLE '$string';");
        }

        return $this->execute("TRUNCATE TABLE '$string';");
    }


    /**
     * 数据库执行
     * @param string $sql 需要执行的sql语句
     * @param array $params 绑定的sql参数
     * @param false $readonly 是否为查询
     * @return array|int
     */
	public function execute(string $sql, array $params = [], bool $readonly = false)
    {
        $start = microtime(true);
        /**
         * @var $sth PDOStatement
         */
        $instance =  $this->dbInstance($this->getDbData()[$this->sqlIndex]);
        $sth = $instance->prepare($sql);

        if ($sth == false){
            $errorInfo = $instance->errorInfo();
             new SqlCheckError($sql,$errorInfo);
        }

        if (is_array($params) && !empty($params)) foreach ($params as $k => $v) {
            if (is_int($v)) {
                $data_type = PDO::PARAM_INT;
            } elseif (is_bool($v)) {
                $data_type = PDO::PARAM_BOOL;
            } elseif (is_null($v)) {
                $data_type = PDO::PARAM_NULL;
            } else {
                $data_type = PDO::PARAM_STR;
            }

            $sth->bindValue($k, $v, $data_type);
        }
        if ($sth->execute()) {
            $end = microtime(true) - $start;
            if (isDebug()) {
                $sqlDefault = $sql;
                foreach ($params as $k => $v) {
                    $sqlDefault = str_replace($k, "\"$v\"", $sqlDefault);
                }
                $GLOBALS["frame"]["sql"][]= ["原始SQL"=>$sql, "填充内容后的SQL"=>$sqlDefault, "执行时间"=>strval($end * 1000) . "ms"];
            }

            return $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();

        }
        $err = $sth->errorInfo();
         new SqlCheckError($sql,$err);
         return 0;
    }

    /**
     * 获取数据库对象
     * @param $db_config
     * @return PDO|null
     */
	public function dbInstance($db_config): ?PDO
    {

        $dsn = [
            "mysql" => "mysql:dbname={$db_config['db']};host={$db_config['host']};port={$db_config['port']}",
            "sqlite3" => "sqlite:".APP_DIR.$db_config['host'],
            "sqlite2" => "sqlite:".APP_DIR.$db_config['host'],
            "sqlserver" => "odbc:Driver={SQL Server};Server={$db_config['host']};Database={$db_config['db']}",
        ];
        $connectData = "";


        try {
            if (!isset($dsn[$this->sqlType]))
                 new AppError("数据库错误: 我们不支持该类型数据库.({$this->sqlType})",$this->db,"type");
            $connectData = $dsn[$this->sqlType];
            if(isDebug())  $GLOBALS["frame"]["sql"][]="数据库信息：".$connectData;
            $key=md5($connectData);
           if(isset(self::$instances[$key]))return self::$instances[$key];

            self::$instances[$key]=new PDO(
                $connectData,
                $db_config['username'],
                $db_config['password'],
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $db_config['charset'] . '\'',
                    PDO::ATTR_PERSISTENT => true,
                ]);
	        return self::$instances[$key];
        } catch (PDOException $e) {
             new AppError('数据库错误: ' . $e->getMessage() . ". 数据库信息：  {$connectData}",$this->db,"type");
        }
        return null;
    }

	/**
	* 输出sql语句
	* @return array
	*/
	public function dumpSql(): array
    {
        return $this->sqlList;
    }
}
