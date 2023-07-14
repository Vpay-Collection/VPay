<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database\driver
 * Class Sqlite
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 23:14
 * Description :
 */

namespace library\database\driver;

use cleanphp\base\Variables;
use library\database\exception\DbConnectError;
use library\database\object\DbFile;
use library\database\object\Model;
use library\database\object\SqlKey;
use PDO;
use PDOException;

class Sqlite extends Driver
{

    private DbFile $dbFile;

    /**
     * @throws DbConnectError
     */
    public function __construct(DbFile $dbFile)
    {
        $this->dbFile = $dbFile;
        //pdo初始化
        try {
            $this->pdo = new PDO("sqlite:" . Variables::getAppPath($this->dbFile->host),
                '',
                '',
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $this->dbFile->charset . '\'',
                ]);
        } catch (PDOException $e) {
            throw new DbConnectError($e->getMessage(), $e->errorInfo, "Sqlite");
        }

    }

    /**
     * 渲染创建字段
     * @param Model $model
     * @param string $table
     * @return string
     */
    function renderCreateTable(Model $model, string $table): string
    {
        $primary_keys = $model->getPrimaryKey() ;
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '`(';
        $name = $primary_keys->name;
        $primary = $name;
        $sql .= $this->renderKey($primary_keys);
        //AUTOINCREMENT
        if ($primary_keys->type === SqlKey::TYPE_INT && $primary_keys->auto) {
            $sql .= " PRIMARY KEY AUTOINCREMENT,";
        } else {
            $sql .= ",";
        }
        foreach (get_object_vars($model) as $key => $value) {
            if ($key ===$primary) continue;
            $sql .= $this->renderKey(new SqlKey($key, $value)) . ",";
        }
        if (str_ends_with($sql, ',')) {
            $sql = substr($sql, 0, strlen($sql) - 1);
        }
        $sql .= ');';

        return $sql;

    }

    public function renderKey(SqlKey $sqlKey): string
    {


        if ($sqlKey->type === SqlKey::TYPE_TEXT && $sqlKey->value !== null)
            $sqlKey->value = str_replace("'", "\'", $sqlKey->value);

        if ($sqlKey->type === SqlKey::TYPE_INT) return "`$sqlKey->name` INTEGER";

        elseif ($sqlKey->type === SqlKey::TYPE_BOOLEAN) return "`$sqlKey->name` INTEGER";

        elseif ($sqlKey->type === SqlKey::TYPE_TEXT  || $sqlKey->type === SqlKey::TYPE_ARRAY) return "`$sqlKey->name` TEXT DEFAULT '$sqlKey->value'";

        elseif ($sqlKey->type === SqlKey::TYPE_FLOAT) return "`$sqlKey->name` REAL DEFAULT '$sqlKey->value'";

        else  return "`$sqlKey->name` TEXT DEFAULT '$sqlKey->value'";
    }

    function getDbConnect(): PDO
    {

        return $this->pdo;
    }

    public function __destruct()
    {
        unset($this->pdo);
    }


    function renderEmpty(string $table): string
    {
        return /** @lang text */ "DELETE FROM  '$table';";
    }

    function onInsertModel(int $model): string
    {
        return '';
    }
}