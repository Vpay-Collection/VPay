<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\objects\config
 * Class NoticeConfig
 * Created By ankio.
 * Date : 2023/7/18
 * Time : 23:34
 * Description :
 */

namespace app\objects\config;

use cleanphp\base\ArgObject;
use library\login\SsoConfig;
use library\mail\MailConfig;
use library\verity\VerityObject;
use library\verity\VerityRule;

class NoticeConfig extends VerityObject
{
    public bool $sso = false;//0 mail 1 sso
    public ArgObject $data;
    public string $admin = "";
    public bool $success_notice = false;//用户支付成功同志
    public bool $daily_notice = false;//收益日报
    public bool $update_notice = false;//安全更新通知
  public function __construct(?array $item = [], $check = true)
  {
      parent::__construct($item, $check);
      if(!$this->sso){
          $this->data = new MailConfig($item,$check);
      }else{
          $this->data = new SsoConfig($item,$check);
      }
  }

    /**
     * 以下通知不允许关闭
     * 1. 支付异常通知
     * 2. 用户退款请求通知
     * 3. 交易失败通知
     */
    function getRules(): array
    {
        return [
            "admin"=>new VerityRule(VerityRule::MAIL,"收件人邮箱错误"),
            "type"=>new VerityRule("^0|1$","通知使用模式错误")
        ];
    }

    function onToArray(string $key, mixed &$value, &$ret): void
    {
        if ($key === "data") {
            if ($value instanceof ArgObject) {
                $ret += $value->toArray();
            } elseif (is_array($value)) {
                $ret += $value ;
            }
            unset($ret[$key]);
        }
        parent::onToArray($key,$value,$ret);
    }

    function onMergeFailed(string $key, mixed $raw, array $object): void
    {
        if ($key === "data"){
            if(!$this->sso){
                $this->data = new MailConfig($object);
            }else{
                $this->data = new SsoConfig($object);
            }
        }
    }

}