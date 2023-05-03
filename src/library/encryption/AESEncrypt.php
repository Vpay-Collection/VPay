<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\encryption;


class AESEncrypt
{

    /**
     * @var string
     */
    private string $secretKey;

    private string $method;


    /**
     * AESEncryptHelper constructor.
     */
    public function __construct($secret_key = '', $method = 'AES-256-CBC')
    {
        if ($secret_key === '') $secret_key = $this->createSecretKey($secret_key);
        $this->secretKey = $secret_key;
        $this->method = $method;
    }

    /**
     * 通过指定id创建key
     * @param $uuid
     * @return string
     */
    public function createSecretKey($uuid): string
    {
        $this->secretKey = md5($this->sha256WithOpenssl($uuid . '|' . uniqid()) . '|' . uniqid());
        return $this->secretKey;
    }

    /**
     * @param $data
     * @return string
     */
    private function sha256WithOpenssl($data): string
    {
        return openssl_digest($data, "sha256");
    }

    /**
     * AES加密
     * @param $data
     * @param int $options
     * @return string
     */
    public function encryptWithOpenssl($data, int $options = 0): string
    {
        $iv = substr($this->secretKey, 8, 16);
        return openssl_encrypt($data, $this->method, $this->secretKey, $options, $iv);
    }

    /**
     * AES解密
     * @param $data
     * @param int $options
     * @return string
     */
    public function decryptWithOpenssl($data, int $options = 0): string
    {
        $iv = substr($this->secretKey, 8, 16);
        return openssl_decrypt($data, $this->method, $this->secretKey, $options, $iv);
    }


}
