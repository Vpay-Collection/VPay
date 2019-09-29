<?php

class Des
{
    /**
     * des-ecb加密
     * @param string $data 要被加密的数据
     * @param string $key 加密密钥(64位的字符串)
     * @return string
     */
    public function encrypt($data, $key){
        return openssl_encrypt ($data, 'des-ecb', $key);
    }

    /**
     * des-ecb解密
     * @param string $data 加密数据
     * @param string $key 加密密钥
     * @return string
     */
    public function decrypt ($data, $key){
        return openssl_decrypt ($data, 'des-ecb', $key);
    }
}