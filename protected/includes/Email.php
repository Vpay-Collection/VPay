<?php
namespace includes;
use lib\email\KL_SENDMAIL_PHPMailer;
use lib\email\kl_sendmail_phpmailerException;
use lib\speed\mvc\Controller;
use model\Config;

class Email{
    private $smtp;
    private $port;
    private $sendmail;
    private $password;
    public function __construct()
    {

        $c=new Config();

        $this->smtp=$c->getData('MailSmtp');
        $this->port=$c->getData('MailPort');
        $this->sendmail=$c->getData('MailSend');
        $this->password=$c->getData('MailPass');
    }
    public function send($mailto,$subject,$content,$fromname,$debug=0){//发送邮件
        $mail = new KL_SENDMAIL_PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        $mail->Port = $this->port;

        $mail->IsSMTP();
        $mail->Host = $this->smtp;
        $mail->SMTPAuth = true;
        $mail->Username = $this->sendmail;
        $mail->Password = $this->password;

        $mail->From = $this->sendmail;
        $mail->FromName = $fromname;

        $mail->do_debug=$debug;

        $mail->AddAddress($mailto);
        $mail->WordWrap = 500;
        $mail->IsHTML(true);
        $mail->Subject = $subject;

        $mail->Body = $content;
        $mail->AltBody = "This is the body in plain text for non-HTML mail clients";
        if($mail->Host == 'smtp.qq.com') $mail->SMTPSecure = "ssl";
        try {
            if (!$mail->Send()) {
                if(!$debug)
                    echo $mail->ErrorInfo;
                return false;
            } else {

                return true;
            }
        } catch (kl_sendmail_phpmailerException $e) {
            if(!$debug)
                echo $mail->ErrorInfo;
            return false;
        }
    }

    public function complieNotify($arr){
        $obj=new Controller();
        $obj->notice1=$arr['notice1'];
        $obj->notice2=$arr['notice2'];
        $obj->notice3=$arr['notice3'];
        return $obj->display('../mail/notify',true);
    }

    public static function isEmail($user_email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
        {
            if (preg_match($chars, $user_email)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

}