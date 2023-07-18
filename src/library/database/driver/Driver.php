<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\database\driver
 * Class Driver
 * Created By ankio.
 * Date : 2022/11/15
 * Time : 21:48
 * Description :
 */

namespace library\database\driver;

use library\database\object\DbFile;
use library\database\object\Model;
use library\database\object\SqlKey;
use PDO;

abstract class Driver
{
    protected ?PDO $pdo = null;

    /**
     * @param DbFile $dbFile 数据库配置类型
     */
    abstract public function __construct(DbFile $dbFile);

    /**
     * 主键渲染
     * @param Model $model
     * @param string $table
     * @return string
     */
    abstract function renderCreateTable(Model $model, string $table): string;

    /**
     * 渲染键值
     * @param SqlKey $sqlKey
     * @return mixed
     */
    abstract function renderKey(SqlKey $sqlKey): mixed;

    /**
     * 获取数据库链接
     * @return PDO
     */
    abstract function getDbConnect(): PDO;

    /**
     * 清空数据表
     * @param $table string 表格
     * @return mixed
     */
    abstract function renderEmpty(string $table): mixed;

    /**
     * 处理插入模式
     * @param $model int 从以下{@link InsertOperation::INSERT_NORMAL}、{@link InsertOperation::INSERT_DUPLICATE}、{@link InsertOperation::INSERT_IGNORE}数据中获取
     * @return string
     */
    abstract function onInsertModel(int $model): string;
}