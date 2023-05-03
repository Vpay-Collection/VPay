<?php
/*
 *  Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\mail;

use cleanphp\base\Config;
use cleanphp\file\Log;
use library\mail\phpmail\Exception;
use library\mail\phpmail\PHPMailer;

class Email
{
    private $smtp;
    private $port;
    private $sendmail;
    private $password;

    public function __construct()
    {
        $conf = Config::getConfig("mail");
        $this->smtp = $conf["smtp"];
        $this->port = $conf["port"];
        $this->sendmail = $conf["send"];
        $this->password = $conf["passwd"];

    }

    public function send($mailto, $subject, $content, $fromname, $debug = 0)
    {//发送邮件\
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //服务器配置
            $mail->CharSet = "UTF-8";                     //设定邮件编码
            $mail->SMTPDebug = $debug;                        // 调试模式输出
            $mail->isSMTP();                             // 使用SMTP
            $mail->Host = $this->smtp;                // SMTP服务器
            $mail->SMTPAuth = true;                      // 允许 SMTP 认证
            $mail->Username = $this->sendmail;                // SMTP 用户名  即邮箱的用户名
            $mail->Password = $this->password;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
            $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
            $mail->Port = $this->port;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
            $mail->setLanguage('zh_cn', __DIR__ . DS . 'phpmail' . DS . 'phpmailer.lang-zh_cn.php');
            $mail->Timeout = 10;
            $mail->setFrom($this->sendmail, $fromname);  //发件人
            $mail->addAddress($mailto);  // 收件人
            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo($this->sendmail); //回复的时候回复给哪个邮箱 建议和发件人一致
            //$mail->addCC('cc@example.com');                    //抄送
            //$mail->addBCC('bcc@example.com');                    //密送

            //发送附件
            // $mail->addAttachment('../xy.zip');         // 添加附件
            // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            //Content
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject = $subject;
            $mail->Body = $content;
            $mail->AltBody = '客户端不支持html显示，请更换邮件客户端。';
            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::record('Mail', $e->getMessage(), Log::TYPE_ERROR);
            Log::record('Mail', $mail->ErrorInfo, Log::TYPE_ERROR);
            return $mail->ErrorInfo;
        }
    }

}