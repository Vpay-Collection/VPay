<?php
namespace app\includes;
use app\model\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
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
    public function send($mailto,$subject,$content,$fromname,$debug=0){//发送邮件\
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
       try {
            //服务器配置
            $mail->CharSet ="UTF-8";                     //设定邮件编码
            $mail->SMTPDebug = $debug;                        // 调试模式输出
            $mail->isSMTP();                             // 使用SMTP
            $mail->Host = $this->smtp;                // SMTP服务器
            $mail->SMTPAuth = true;                      // 允许 SMTP 认证
            $mail->Username = $this->sendmail;                // SMTP 用户名  即邮箱的用户名
            $mail->Password = $this->password;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
            $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
            $mail->Port = $this->port;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
            $mail->setLanguage('zh_cn',APP_LIB.DS.'email'.DS);
            $mail->Timeout=10;
            $mail->setFrom($this->sendmail, $fromname);  //发件人
            $mail->addAddress($mailto);  // 收件人
            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo($this->sendmail); //回复的时候回复给哪个邮箱 建议和发件人一致
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject = $subject;
            $mail->Body    = $content;
            $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

            $mail->send();
            return true;
        } catch (Exception $e) {
           if($debug>0){
               echo '邮件发送失败: ', $mail->ErrorInfo,'<br>';

           }
           return false;

       }
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