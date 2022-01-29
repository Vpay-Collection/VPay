<?php


namespace app\lib\Encryption;

/**
 * Class AESEncryptHelper
 * @package Security\DataSecurity
 */
class AESEncryptHelper
{

    const SHA256 = 'sha256';

    const METHOD = 'AES-256-CBC';

    /**
     * @var string
     */
    private string $secretKey = '0123456789012345678901';



    /**
     * AESEncryptHelper constructor.
     */
    public function __construct($secret_key)
    {
        $this->secretKey = $secret_key;
    }


    /**
     * @param $data
     * @param int $options
     * @return string
     */
    public function encryptWithOpenssl($data, int $options = 0): string
    {
        $iv = substr($this->secretKey, 8, 16);
        return openssl_encrypt($data, self::METHOD, $this->secretKey, $options, $iv);
    }


    /**
     * @param $data
     * @param int $options
     * @return string
     */
    public function decryptWithOpenssl($data, int $options = 0): string
    {
        $iv = substr($this->secretKey, 8, 16);
        return openssl_decrypt($data, self::METHOD, $this->secretKey, $options, $iv);
    }


    /**
     * @param $uuid
     * @return string
     */
    public function createSecretKey($uuid): string
    {
        $this->secretKey  = md5($this->sha256WithOpenssl($uuid . '|' . $this->secretKey) . '|' . $this->secretKey);
        return  $this->secretKey;
    }


    /**
     * @param $data
     * @return string
     */
    private function sha256WithOpenssl($data): string
    {
        return openssl_digest($data, self::SHA256);
    }


}
