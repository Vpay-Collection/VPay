<?php
namespace app\includes;
/**
 * Created by PhpStorm.
 * User: dreamn
 * Date: 2019-04-27
 * Time: 09:54
 */
class Web
{
    /**
     * @param $url
     * @param $array
     * @return mixed
     */
    function post($url, $array, $header = [])
    {
        $curl = curl_init();
        //设置提交的url
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
        //设置post数据
        $post_data = $array;
        @curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //获得数据并返回
        return $data;
    }

    function get($url,$param=array(),$useragent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:73.0) Gecko/20100101 Firefox/73.0")
    {

        if(!is_array($param)){
            throw new Exception("参数必须为array");
        }
        $p=http_build_query($param);
        if(preg_match('/\?[\d\D]+/',$url)){//matched ?c
            $url.='&'.$p;
        }else if(preg_match('/\?$/',$url)){//matched ?$
            $url.=$p;
        }else{
            $url.='?'.$p;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        //参数为1表示传输数据，为0表示直接输出显示。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //参数为0表示不带头文件，为1表示带头文件
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);


        return $output;
    }
};