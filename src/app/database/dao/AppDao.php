<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class AppDao
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 11:24
 * Description :
 */

namespace app\database\dao;

use app\database\model\AppModel;
use library\database\object\Dao;

class AppDao extends Dao
{

    public function __construct()
    {
        parent::__construct(AppModel::class);
    }
    /**
     * @inheritDoc
     */
    protected function getTable(): string
    {
        return "application";
    }

    public function getByAppId($appid): ?AppModel
    {

        return $this->find(null, ['id' => $appid]);
    }

    public function getAllApp()
    {
        return $this->select()->commit(false);
    }

    public function del($id)
    {
        if ($id === 0) return;
        $this->delete()->where(['id' => $id])->commit();
    }

}