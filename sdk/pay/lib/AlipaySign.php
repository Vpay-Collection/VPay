<?php
/**
 * Created by PhpStorm.
 * User: dreamn
 * Date: 2019-09-25
 * Time: 20:30
 */
class AlipaySign
{//支付宝的参数签名算法
    /**
     * 生成签名
     * @param $Arr
     * @param $key
     * @return string [type]      [description]
     */
    public function getSign($Arr, $key)
    {
        foreach ($Arr as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        if (!empty($key)) {
            $String = $String . "&key=" . $key;
        }
        //echo "【string2】".$String."</br>";
        //签名步骤三：sha256加密

        $String = hash('sha256',$String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     *    作用：格式化参数，签名过程需要使用
     */
    private function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = "";
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }



}