<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * File VerityTrait.php
 * Created By ankio.
 * Date : 2023/1/2
 * Time : 01:29
 * Description :
 */

namespace library\verity;

use cleanphp\App;
use cleanphp\file\Log;
use cleanphp\objects\StringBuilder;

trait VerityTrait
{
    /**
     * @throws VerityException
     */
    public function onParseType(string $key, &$val, $demo)
    {
        if (!is_string($val)) return;
        $rules = $this->getRules();

        /**
         * @var $rule ?VerityRule
         */
        if (!isset($rules[$key])) {
            $rule = null;
            foreach ($rules as $k => $v) {
                if ((new StringBuilder())->contains("|")) {
                    foreach (explode("|", $k) as $vv) {
                        if ($vv === $key) {
                            $rule = $v;
                            break;
                        }
                    }
                    if (!empty($rule)) break;
                }
            }
            if (empty($rule)) return;
        } else {
            $rule = $rules[$key];
        }

        App::$debug && Log::record('Verity', sprintf(" 规则: %s 验证数据：%s", $rule->rule, $val), Log::TYPE_WARNING);

        //检查空值
        if (empty($rule->rule) && !$rule->allow_empty && empty($val)) {
            throw new VerityException($rule->msg, $key, $val);
        }
        //空值不验证,未曾通过校验直接抛异常
        if (!empty(strval($val)) && $val !== $demo && !VerityRule::check($rule->rule, $val)) {
            throw new VerityException($rule->msg, $key, $val);
        }
    }
}