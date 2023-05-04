<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\encryption;

/**
 * Class RSAEncryptHelper
 */
class RSAEncrypt
{
    /**
     * @var array
     */
    private array $config = ['public_key' => '', 'private_key' => ''];

    /**
     * 获取密钥
     * @return array|string[]
     */
    public function getKey(): array
    {
        return $this->config;
    }

    /**
     * 创建密钥
     * @param array $config
     * @return array
     */
    public function create(array $config = []): array
    {
        if ($config === []) {
            $config = [
                "digest_alg" => "sha512",
                "private_key_bits" => 4096,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ];
        }

        $res = openssl_pkey_new($config);

        openssl_pkey_export($res, $private_key);

        $public_key = openssl_pkey_get_details($res);

        $this->config = ['public_key' => $public_key["key"], 'private_key' => $private_key];

        return $this->config;
    }

    /**
     * 初始化密钥
     * @param $private_key
     * @param $public_key
     * @return void
     */
    public function initRSAData($private_key, $public_key)
    {
        $this->config['private_key'] = $private_key;
        $this->config['public_key'] = $public_key;
    }

    /**
     * 初始化密钥存储路径
     * @param $private_key_filepath
     * @param $public_key_filepath
     * @throws EncryptionException
     */
    public function initRSAPath($private_key_filepath, $public_key_filepath)
    {
        $this->config['private_key'] = $this->getContents($private_key_filepath);
        $this->config['public_key'] = $this->getContents($public_key_filepath);
    }


    /**
     * 获取指定地址的路径
     * @param $file_path
     * @return bool|string
     * @throws EncryptionException
     */
    private function getContents($file_path)
    {
        if (!file_exists($file_path))
            throw new EncryptionException("指定路径的密钥文件不存在：$file_path");
        return file_get_contents($file_path);
    }


    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public function rsaPublicEncrypt(string $data = ''): ?string
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data, $encrypted, $this->getPublicKey()) ? base64_encode($encrypted) : null;
    }


    /**
     * 获取公钥
     * @return resource
     */
    private function getPublicKey()
    {
        $public_key = $this->config['public_key'];
        return openssl_pkey_get_public($public_key);
    }


    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public function rsaPrivateDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->getPrivateKey())) ? $decrypted:null;
    }


    /**
     * 获取私钥
     * @return resource
     */
    private function getPrivateKey()
    {
        $private_key = $this->config['private_key'];
        return openssl_pkey_get_private($private_key);
    }


    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public function rsaPrivateEncrypt(string $data = ''): ?string
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data, $encrypted, $this->getPrivateKey()) ? base64_encode($encrypted) : null;
    }


    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public function rsaPublicDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, $this->getPublicKey())) ? $decrypted:null;
    }
}
