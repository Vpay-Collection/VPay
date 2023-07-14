<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database
 * Class Db
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 23:19
 * Description :
 */

namespace library\database;

use cleanphp\App;
use cleanphp\base\Error;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\exception\ExtendError;
use cleanphp\file\File;
use cleanphp\file\Log;
use cleanphp\objects\StringBuilder;
use library\database\driver\Driver;
use library\database\exception\DbExecuteError;
use library\database\object\Dao;
use library\database\object\DbFile;
use library\database\object\Model;
use PDO;
use PDOException;
use PDOStatement;

class Db
{
    private ?Driver $db = null;

    private ?DbFile $dbFile = null;

    /**
     * 构造函数
     * @param DbFile $dbFile 数据库配置类
     * @throws ExtendError
     */
    public function __construct(DbFile $dbFile)
    {
        if (!class_exists("PDO")) {
            throw new ExtendError(sprintf("缺少PDO拓展支持，请安装PDO拓展并启用。%s", "https://www.php.net/manual/zh/pdo.installation.php"), "pdo");
        }

        if (empty($dbFile->type)) {
            Error::err("数据库驱动错误，没有为数据库配置设置数据库驱动", [], "Database Driver");
        }

        $this->dbFile = $dbFile;
        //select driver
        $drive_cls = "library\\database\\driver\\" . ucfirst($dbFile->type);

        if (class_exists($drive_cls)) {
            $this->db = new $drive_cls($dbFile);
        } elseif (class_exists($dbFile->type)) {
            //如果此处指定数据库驱动，则尝试去加载
            $cls = $dbFile->type;
            $this->db = new $cls($dbFile);
        } else {
            Error::err(sprintf("未找到对应的数据库驱动：%s", $dbFile->type), [], "Database Driver");
        }
    }

    /**
     * 使用指定数据库配置初始化数据库连接
     * @param DbFile $dbFile
     * @return Db
     */
    public static function init(DbFile $dbFile): Db
    {

        $hash = $dbFile->hash();
        $instance = Variables::get("db_$hash");
        if (empty($instance)) {
            $instance = new self($dbFile);
            Variables::set("db_$hash",$instance);
        }

        return $instance;
    }

    /**
     * 数据表初始化
     * @param Dao $dao
     * @param Model $model
     * @param string $table
     * @return void
     * @throws DbExecuteError
     */
    function initTable(Dao $dao, Model $model, string $table): void
    {

        App::$debug && Log::record("SQL", sprintf("创建数据表 `%s`", $table));
        $this->execute($this->db->renderCreateTable($model, $table));
        $dao->onCreateTable();
    }

    /**
     * 数据库执行
     * @param string $sql 需要执行的sql语句
     * @param array $params 绑定的sql参数
     * @param false $readonly 是否为查询
     * @param bool $cache 是否缓存
     * @param array $tables
     * @return array|int
     * @throws DbExecuteError
     */
    public function execute(string $sql, array $params = [], bool $readonly = false, bool $cache = false, array $tables = []): int|array
    {
        $shouldCache = $readonly && $cache && !StringBuilder::init($sql)->contains("like");

        $cacheDir = Variables::getCachePath("sql",DS);

        $baseTables = join("_",$tables);
        if(!in_array($baseTables,$tables)){
            $tables[] = $baseTables;
        }
        if($shouldCache ){

           $data = Cache::init(0,$cacheDir.$baseTables.DS)->get($sql.join(',',$params));
           if(!empty($data)){
               return $data;
           }
        }elseif(!$readonly){
            //删除缓存,数据库数据发生变化后清除缓存
            foreach ($tables as $table){
                Cache::init(0,$cacheDir.$table.DS)->empty();
            }

        }

        App::$debug && Variables::set("__db_sql_start__", microtime(true));
        /**
         * @var $sth PDOStatement
         */
        $sth = $this->db->getDbConnect()->prepare($sql);

        if (!$sth) {
            throw new DbExecuteError(sprintf("Sql语句【%s】预编译出错：%s", $sql, implode(" , ", $this->db->getDbConnect()->errorInfo())));
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
        $ret_data = null;
        try{
            if ($sth->execute()) {
                $ret_data = $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();
            }
        }catch (PDOException $exception){
            throw new DbExecuteError(sprintf("执行SQL语句出错：\r\n%s\r\n\r\n错误信息：%s", $this->highlightSQL($sql), $exception->getMessage()));
        }
        if (App::$debug) {
            $end = microtime(true) - Variables::get("__db_sql_start__");
            Variables::del("__db_sql_start__");
            $sql_default = $sql;
            $params = array_reverse($params);
            foreach ($params as $k => $v) {
                $sql_default = str_replace($k, "\"$v\"", $sql_default);
            }
            Log::record("SQL", $sql_default);
            Log::record("SQL", sprintf("执行时间：%s 毫秒", $end * 1000));
        }
        if ($ret_data !== null) {
            if($shouldCache && !empty($ret_data)){
                Cache::init(0,$cacheDir.$baseTables.DS)->set($sql.join(',',$params),$ret_data);
            }
            return $ret_data;
        }
        throw new DbExecuteError(sprintf("执行SQL语句出错：\r\n%s\r\n\r\n错误信息：%s", $this->highlightSQL($sql), $sth->errorInfo()[2]));
    }

    private function highlightSQL($sql)
    {
        if (!Variables::get("sql_highlight")) {
            return $sql;
        }

        // 定义 SQL 关键词列表
        $keywords = array(
            'SELECT', 'FROM', 'WHERE', 'AND', 'OR', 'NOT', 'IN', 'BETWEEN', 'LIKE',
            'IS', 'NULL', 'AS', 'INNER', 'JOIN', 'LEFT', 'RIGHT', 'OUTER', 'ON',
            'GROUP', 'BY', 'HAVING', 'ORDER', 'LIMIT', 'OFFSET', 'INSERT', 'INTO',
            'VALUES', 'UPDATE', 'SET', 'DELETE', 'TRUNCATE', 'CREATE', 'TABLE',
            'ALTER', 'DROP', 'INDEX', 'VIEW', 'GRANT', 'REVOKE', 'UNION', 'ALL',
            'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'PRIMARY', 'KEY', 'FOREIGN',
            'REFERENCES', 'CASCADE', 'CONSTRAINT'
            // 可根据需要添加其他关键词
        );

        // 标记关键词
        $highlightedSQL = preg_replace_callback('/\b(' . implode('|', $keywords) . ')\b/i', function ($matches) {
            return '<span style="color: blue;">' . $matches[0] . '</span>';
        }, $sql);

        // 标记字符串值
        $highlightedSQL = preg_replace_callback("/'(.*?)'/i", function ($matches) {
            return '<span style="color: green;">' . $matches[0] . '</span>';
        }, $highlightedSQL);

        // 标记数字值
        $highlightedSQL = preg_replace_callback("/\b\d+\b/", function ($matches) {
            return '<span style="color: orange;">' . $matches[0] . '</span>';
        }, $highlightedSQL);

        // 标记参数绑定
        $highlightedSQL = preg_replace_callback("/:([\w]+)/i", function ($matches) {
            return '<span style="color: purple;">' . $matches[0] . '</span>';
        }, $highlightedSQL);

        // 标记表名和字段名
        $highlightedSQL = preg_replace_callback('/(`?[\w]+`?)/i', function ($matches) {
            return '<span style="color: red;">' . $matches[0] . '</span>';
        }, $highlightedSQL);

        // 标记注释
        $highlightedSQL = preg_replace('/--.*$/m', '<span style="color: gray;">$0</span>', $highlightedSQL);

        return $highlightedSQL;
    }




    public function __destruct()
    {
        unset($this->db);
        Variables::del($this->dbFile->hash());
    }

    /**
     * 获取数据库驱动
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->db;
    }

    /**
     * 导入数据表
     * @param string $sql_path
     * @return void
     */
    public function import(string $sql_path)
    {
        $data = $sql_path;
        if (file_exists($data)) $data = file_get_contents($data);
        $this->db->execute($data);
    }

    /**
     * 导出数据表
     * @param ?string $output 输出路径
     * @param bool $only_struct 是否只导出结构
     * @return string
     */
    public function export(string $output = null, bool $only_struct = false): string
    {

        $result = $this->execute("show tables", [], true);
        $tabList = [];
        foreach ($result as $value) {
            $tabList[] = $value["Tables_in_dx"];
        }
        $info = "-- ----------------------------\r\n";
        $info .= "-- Powered by CleanPHP\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "-- 日期：" . date("Y-m-d H:i:s", time()) . "\r\n";
        $info .= "-- ----------------------------\r\n\r\n";

        foreach ($tabList as $val) {
            $sql = "show create table " . $val;
            $result = $this->execute($sql, [], true);
            $info .= "-- ----------------------------\r\n";
            $info .= "-- Table structure for `" . $val . "`\r\n";
            $info .= "-- ----------------------------\r\n";
            $info .= "DROP TABLE IF EXISTS `" . $val . "`;\r\n";
            $info .= $result[0]["Create Table"] . ";\r\n\r\n";
        }

        if (!$only_struct) {
            foreach ($tabList as $val) {
                $sql = "select * from " . $val;
                $result = $this->execute($sql, [], true);
                if (count($result) < 1) continue;
                $info .= "-- ----------------------------\r\n";
                $info .= "-- Records for `" . $val . "`\r\n";
                $info .= "-- ----------------------------\r\n";

                foreach ($result as $value) {
                    $sqlStr = /** @lang text */
                        "INSERT INTO `" . $val . "` VALUES (";
                    foreach ($value as $k) {
                        $sqlStr .= "'" . $k . "', ";
                    }
                    $sqlStr = substr($sqlStr, 0, strlen($sqlStr) - 2);
                    $sqlStr .= ");\r\n";
                    $info .= $sqlStr;
                }


            }
        }
        if ($output !== null) {
            File::mkDir(dirname($output));
            file_put_contents($output, $info);
        }

        return $info;
    }


}