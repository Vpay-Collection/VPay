<?php

namespace app\lib\speed\mvc;


use app\Error;
use PDO;
use PDOException;

/**
 * Class Model
 * @package lib\speed\mvc
 */
class Model
{

    public $page;
    /**
     * 数据表名称
     * @var string $table_name
     */
    protected $table_name;


    private $sql = array();

    /**
     * Model constructor.
     * @param null $table_name
     */
    public function __construct($table_name = null)
    {
        if ($table_name) $this->table_name = $table_name;
    }

    /**
     * 输出执行的sql语句
     * @return array
     */
    public function dumpSql()
    {
        return $this->sql;
    }

    /**
     * 重置操作的数据表，便于变换
     * @param $table_name
     */
    protected function reset($table_name)
    {
        $this->table_name = $table_name;

    }

    /**
     * 查询一条数据
     * @param array $conditions
     * @param null $sort
     * @param string $fields
     * @return bool|mixed
     */
    protected function select($conditions = array(), $sort = null, $fields = '*')
    {
        $res = $this->selectAll($conditions, $sort, $fields, 1);
        return !empty($res) ? array_pop($res) : false;
    }

    /**
     * 数据查询
     * @param array $conditions
     * @param null $sort
     * @param string $fields
     * @param null $limit
     * @return array|mixed
     */
    protected function selectAll($conditions = array(), $sort = null, $fields = '*', $limit = null)
    {
        $sort = !empty($sort) ? ' ORDER BY ' . $sort : '';
        $conditions = $this->_where($conditions);

        $sql = ' FROM ' . $this->table_name . $conditions["_where"];
        if (is_array($limit)) {
            $total = $this->query('SELECT COUNT(*) as M_COUNTER ' . $sql, $conditions["_bindParams"]);
            if (!isset($total[0]['M_COUNTER']) || $total[0]['M_COUNTER'] == 0) return array();

            $limit = $limit + array(1, 10, 10);
            $limit = $this->pager($limit[0], $limit[1], $limit[2], $total[0]['M_COUNTER']);
            $limit = empty($limit) ? '' : ' LIMIT ' . $limit['offset'] . ',' . $limit['limit'];
        } else {
            $limit = !empty($limit) ? ' LIMIT ' . $limit : '';
        }
        return $this->query('SELECT ' . $fields . $sql . $sort . $limit, $conditions["_bindParams"]);
    }

    /**
     * condition的处理函数
     * @param $conditions
     * @return array
     */
    private function _where($conditions)
    {

        $result = array("_where" => " ", "_bindParams" => array());
        if (is_array($conditions) && !empty($conditions)) {

            $sql = null;
            $join = array();
            reset($conditions);
            $first = key($conditions);

            if (is_int($first) && $sql = $conditions[$first]) unset($conditions[$first]);


            foreach ($conditions as $key => $condition) {

                if (is_int($key)) {
                    $join[] = $condition;
                    unset($conditions[$key]);
                    continue;
                }
                if (substr($key, 0, 1) != ":") {
                    unset($conditions[$key]);
                    $conditions[":" . str_replace('.', '_', $key)] = $condition;
                }
                $join[] = "`" . str_replace('.', '`.`', $key) . "` = :" . str_replace('.', '_', $key);
            }

            if (!$sql) $sql = join(" AND ", $join);

            //var_dump($first,$sql,$conditions);
            $result["_where"] = " WHERE " . $sql;
            $result["_bindParams"] = $conditions;
        }
        return $result;
    }

    /**
     * 直接执行的查询语句
     * @param $sql
     * @param array $params
     * @return mixed
     */
    protected function query($sql, $params = array())
    {
        return $this->execute($sql, $params, true);
    }

    /**
     * 直接执行sql语句
     * @param string $sql sql语句
     * @param array $params
     * @param bool $readonly
     * @return mixed
     */
    public function execute($sql, $params = array(), $readonly = false)
    {
        /**
         * @var $sth PDO
         */
        $this->sql[] = $sql;


        if ($readonly && !empty($GLOBALS['mysql']['MYSQL_SLAVE'])) {
            $slave_key = array_rand($GLOBALS['mysql']['MYSQL_SLAVE']);
            $sth = $this->dbInstance($GLOBALS['mysql']['MYSQL_SLAVE'][$slave_key], 'slave_' . $slave_key)->prepare($sql);
        } else {
            $sth = $this->dbInstance($GLOBALS['mysql'], 'master')->prepare($sql);
        }

        if (is_array($params) && !empty($params)) foreach ($params as $k => &$v) {
            if (is_int($v)) {
                $data_type = PDO::PARAM_INT;
            } elseif (is_bool($v)) {
                $data_type = PDO::PARAM_BOOL;
            } elseif (is_null($v)) {
                $data_type = PDO::PARAM_NULL;
            } else {
                $data_type = PDO::PARAM_STR;
            }
            $sth->bindParam($k, $v, $data_type);
        }

        if ($sth->execute()) return $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();
        $err = $sth->errorInfo();
        Error::err('Database SQL: "' . $sql . '", ErrorInfo: ' . $err[2]);
        return false;
    }

    /**
     * 数据库初始化
     * @param $db_config
     * @param $db_config_key
     * @param bool $force_replace
     * @return mixed
     */
    protected function dbInstance($db_config, $db_config_key, $force_replace = false)
    {
        if ($force_replace || empty($GLOBALS['mysql_instances'][$db_config_key])) {
            try {
                if (!class_exists("PDO") || !in_array("mysql", PDO::getAvailableDrivers(), true)) {
                    Error::err('Database Err: PDO or PDO_MYSQL doesn\'t exist!');
                }
                $GLOBALS['mysql_instances'][$db_config_key] = new PDO('mysql:dbname=' . $db_config['MYSQL_DB'] . ';host=' . $db_config['MYSQL_HOST'] . ';port=' . $db_config['MYSQL_PORT'], $db_config['MYSQL_USER'], $db_config['MYSQL_PASS'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $db_config['MYSQL_CHARSET'] . '\''));
            } catch (PDOException $e) {
                Error::err('Database Err: ' . $e->getMessage());
            }
        }
        return $GLOBALS['mysql_instances'][$db_config_key];
    }

    /**
     * 分页处理函数
     * @param $page
     * @param int $pageSize
     * @param int $scope
     * @param int $total
     * @return array|null
     */
    protected function pager($page, $pageSize = 10, $scope = 10, $total = 0)
    {
        $this->page = null;
        if ($total > $pageSize) {
            $total_page = ceil($total / $pageSize);
            $page = min(intval(max($page, 1)), $total_page);
            $this->page = array(
                'total_count' => $total,//总数量
                'page_size' => $pageSize,//一页大小
                'total_page' => $total_page,//总页数
                'first_page' => 1,//第一页
                'prev_page' => ((1 == $page) ? 1 : ($page - 1)),//上一页
                'next_page' => (($page == $total_page) ? $total_page : ($page + 1)),//下一页
                'last_page' => $total_page,//最后一页
                'current_page' => $page,//当前页
                'all_pages' => array(),//所有页
                'offset' => ($page - 1) * $pageSize,
                'limit' => $pageSize,
            );
            $scope = (int)$scope;
            if ($total_page <= $scope) {
                $this->page['all_pages'] = range(1, $total_page);
            } elseif ($page <= $scope / 2) {
                $this->page['all_pages'] = range(1, $scope);
            } elseif ($page <= $total_page - $scope / 2) {
                $right = $page + (int)($scope / 2);
                $this->page['all_pages'] = range($right - $scope + 1, $right);
            } else {
                $this->page['all_pages'] = range($total_page - $scope + 1, $total_page);
            }
        }
        return $this->page;
    }

    /**
     * 更新数据
     * @param $conditions
     * @param $row
     * @return mixed
     */
    protected function update($conditions, $row)
    {
        $values = array();
        foreach ($row as $k => $v) {
            $values[":M_UPDATE_" . $k] = $v;
            $setstr[] = "`{$k}` = " . ":M_UPDATE_" . $k;
        }
        $conditions = $this->_where($conditions);
        return $this->execute("UPDATE " . $this->table_name . " SET " . implode(', ', $setstr) . $conditions["_where"], $conditions["_bindParams"] + $values);
    }

    /**
     * 自减
     * @param $conditions
     * @param $field
     * @param int $optval
     * @return mixed
     */
    protected function decr($conditions, $field, $optval = 1)
    {
        return $this->incr($conditions, $field, -$optval);
    }

    /**
     * 自增
     * @param $conditions
     * @param $field
     * @param int $optval
     * @return mixed
     */
    protected function incr($conditions, $field, $optval = 1)
    {
        $conditions = $this->_where($conditions);
        return $this->execute("UPDATE " . $this->table_name . " SET `{$field}` = `{$field}` + :M_INCR_VAL " . $conditions["_where"], $conditions["_bindParams"] + array(":M_INCR_VAL" => $optval));
    }

    /**
     * 删除一条记录
     * @param $conditions
     * @return mixed
     */
    protected function delete($conditions)
    {
        $conditions = $this->_where($conditions);
        return $this->execute("DELETE FROM " . $this->table_name . $conditions["_where"], $conditions["_bindParams"]);
    }

    /**
     * 插入一条记录
     * @param $row
     * @return mixed
     */
    protected function insert($row)
    {
        $values = array();
        foreach ($row as $k => $v) {
            $keys[] = "`{$k}`";
            $values[":" . $k] = $v;
            $marks[] = ":" . $k;
        }
        $this->execute("INSERT INTO " . $this->table_name . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $marks) . ")", $values);
        return $this->dbInstance($GLOBALS['mysql'], 'master')->lastInsertId();
    }

    /**
     * 插入或更新，如果该记录存在，则根据colums指定的更新字段对该记录进行更新
     * @param array|string $row 需要插入的字段
     * @param array $colums 需要更新的字段
     * @return mixed
     */
    protected function insertDuplicate($row, $colums)
    {
        $values = array();
        foreach ($row as $k => $v) {
            $keys[] = "`{$k}`";
            $values[":" . $k] = $v;
            $marks[] = ":" . $k;
        }
        $update = array();
        foreach ($colums as $k) {
            $update[] = $k . "=:" . $k;
        }
        $this->execute("INSERT INTO " . $this->table_name . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $marks) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $update), $values);
        return $this->dbInstance($GLOBALS['mysql'], 'master')->lastInsertId();
    }

    /**
     * 若重复插入，则忽略重复值，即不更新也不插入
     * @param array|string array $row 需要插入的字段
     * @return mixed
     */
    protected function insertIgnore($row)
    {
        $values = array();
        foreach ($row as $k => $v) {
            $keys[] = "`{$k}`";
            $values[":" . $k] = $v;
            $marks[] = ":" . $k;
        }
        $this->execute("INSERT IGNORE INTO " . $this->table_name . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $marks) . ")", $values);
        return $this->dbInstance($GLOBALS['mysql'], 'master')->lastInsertId();
    }

    /**
     * 统计查出来的数据的总数
     * @param array|string $conditions
     * @return int|mixed
     */
    protected function selectCount($conditions)
    {
        $conditions = $this->_where($conditions);
        $count = $this->query("SELECT COUNT(*) AS M_COUNTER FROM " . $this->table_name . $conditions["_where"], $conditions["_bindParams"]);
        return isset($count[0]['M_COUNTER']) && $count[0]['M_COUNTER'] ? $count[0]['M_COUNTER'] : 0;
    }

    /**
     * 对某个字段进行求和
     * @param array|string $conditions
     * @param string $param
     * @return int|mixed
     */
    protected function selectSum($conditions, $param)
    {
        $conditions = $this->_where($conditions);
        $count = $this->query("SELECT SUM($param) AS M_COUNTER FROM " . $this->table_name . $conditions["_where"], $conditions["_bindParams"]);
        return isset($count[0]['M_COUNTER']) && $count[0]['M_COUNTER'] ? $count[0]['M_COUNTER'] : 0;
    }
}