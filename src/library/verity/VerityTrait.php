<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
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

trait VerityTrait
{
    /**
     * @throws VerityException
     */
    public function onParseTypeCheck(string $key, &$val, $demo): void
    {
        if (!is_string($val)||!$this->check) return ;
        $rules = $this->getRules();

        /**
         * @var $rule ?VerityRule
         */
        if (!isset($rules[$key])) {
            $rule = null;
            foreach ($rules as $k => $v) {
                if (str_contains($k,"|")) {
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
        App::$debug && Log::record('Verity', sprintf(" 规则: %s 验证数据：%s", $rule->rule??"", $val), Log::TYPE_WARNING);

        //检查空值
        if (empty($rule->rule)) {
            if(!$rule->allow_empty && empty($val)){
                throw new VerityException($rule->msg, $key, $val);
            }

        }else{
            if ((!empty($val)  && $val !== $demo && !VerityRule::check($rule->rule, $val))||empty($val)) {
                throw new VerityException($rule->msg, $key, $val);
            }
        }



    }
}