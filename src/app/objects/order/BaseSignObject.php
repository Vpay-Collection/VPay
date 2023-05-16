<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\objects
 * Class BaseSignObject
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 11:19
 * Description :
 */

namespace app\objects\order;

use app\database\dao\AppDao;
use app\database\model\AppModel;
use library\login\SignUtils;
use library\verity\VerityException;
use library\verity\VerityObject;
use library\verity\VerityRule;

class BaseSignObject extends VerityObject
{
    public string $sign = "";//签名
    public int $t = 0;//时间戳
    public string $appid = "";//应用

    protected ?AppModel $appModel = null;

    /**
     * @throws VerityException
     */
    public function __construct(array $item = [])
    {
        parent::__construct($item);
        $array = $this->toArray();
        $data = AppDao::getInstance()->getByAppId($this->appid);
        if (empty($data)) {
            throw new VerityException('应用不存在');
        }
        $this->appModel = $data;
        $key = $data->app_key;
        if (!SignUtils::checkSign($array, $key)) {
            throw new VerityException('签名验证失败');
        }
        if (time() - $this->t > 300) {
            throw new VerityException('API超时');
        }
    }

    /**
     * 重写校验规则
     * @return string[]
     */
    function getRules(): array
    {
        return [
            't' => new VerityRule('^\d{10}$', "时间戳错误", false),
            'sign' => new VerityRule('^\w{64}$', "签名错误", false),
            // 'appid' => new VerityRule('^\w{16}$', "发起订单的应用错误", false)
        ];
    }

    /**
     * 获取app
     * @return AppModel
     */
    function getApp(): AppModel
    {
        return $this->appModel;
    }

}