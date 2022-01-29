<?php

namespace app\lib\Encryption;

/**
 * Class RSAEncryptHelper
 * @package Security\DataSecurity
 */
class RSAEncryptHelper
{
    /**
     * @var array
     */
    private array $config = array('public_key' => '', 'private_key' => '');

    /**
     * RSAEncryptHelper constructor.
     */
    public function __construct()
    {
    }

    public function getKey(): array
    {
        return $this->config;
    }
    public function create(){
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

// Create the private and public key
        $res = openssl_pkey_new($config);

// Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

// Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);

        $this->config = array('public_key' => $pubKey["key"], 'private_key' => $privKey);

        //var_dump($this->config);

    }

    /**
     * 初始化密钥
     * @param $private_key
     * @param $public_key
     * @return void
     */
    public function initRSAData($private_key, $public_key)
    {
        $this->config['private_key'] = $this->getContents($private_key);
        $this->config['public_key'] = $this->getContents($public_key);
    }
    /**
     * @param $private_key_filepath
     * @param $public_key_filepath
     */
    public function initRSAPath($private_key_filepath, $public_key_filepath)
    {
        $this->config['private_key'] = $this->getContents($private_key_filepath);
        $this->config['public_key'] = $this->getContents($public_key_filepath);
    }


    /**
     * @param $file_path
     * @return bool|string
     */
    private function getContents($file_path)
    {
        file_exists($file_path) or exitApp('密钥或公钥的文件路径错误');
        return file_get_contents($file_path);
    }


    /**
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
     * @return resource
     */
    private function getPublicKey()
    {
        $public_key = $this->config['public_key'];
        return openssl_pkey_get_public($public_key);
    }


    /**
     * @param string $encrypted
     * @return null
     */
    public function rsaPrivateDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->getPrivateKey())) ? $decrypted : null;
    }


    /**
     * @return false|resource
     */
    private function getPrivateKey()
    {
        $priv_key = $this->config['private_key'];
        return openssl_pkey_get_private($priv_key);
    }


    /**
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
     * @param string $encrypted
     * @return null
     */
    public function rsaPublicDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, $this->getPublicKey())) ? $decrypted : null;
    }
}
