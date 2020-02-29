<?php
namespace lib\email;
use Exception;

/**
 * kl_sendmail_phpmailerException.php
 * User: Dreamn
 * Date: 2020/1/17 16:53
 * Description:
 */
class kl_sendmail_phpmailerException extends Exception {
    public function errorMessage() {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}