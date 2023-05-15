<?php
/*
 *  Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\mail
 * Class Mail
 * Created By ankio.
 * Date : 2022/11/30
 * Time : 20:58
 * Description :
 */

namespace library\mail;

use cleanphp\App;


class AnkioMail
{
    /**
     * @param $mailto string 发送对象
     * @param $subject string 主题
     * @param $content string 发送的内容
     * @param $fromname string 发送者的昵称
     * @return bool|string
     */
    static function send(string $mailto, string $subject, string $content, string $fromname, $debug = false)
    {
        return (new Email())->send($mailto, $subject, $content, $fromname, $debug ? 1 : 0);
    }

    static function compileNotify($bg_color, $color, $logo, $site_name, $title, $body): string
    {
        $date = date("Y-m-d H:i:s");
        return <<<EOF
<body style="color: #666; font-size: 14px; font-family: 'Open Sans',Helvetica,Arial,sans-serif;">
<div class="box-content" style=" margin: 20px auto;  max-width: 600px;">
    <div class="header-tip"  style="font-size: 12px;color: #aaa;text-align: right;padding-right: 25px;padding-bottom: 10px;"> Powered by Ankio </div>
    <div class="info-top"
         style="padding: 15px 25px;border-top-left-radius: 10px;border-top-right-radius: 10px;background: {$bg_color};color: #fff;overflow: hidden;line-height: 32px;">
        <img src="{$logo}" style="float: left; margin: 0 10px 0 0; width: 32px;" alt="">
        <div style="color:{$color}"><strong>{$title}</strong></div>
    </div>
    <div class="info-wrap" style="border:1px solid #ddd;overflow: hidden;padding: 15px 15px 20px;">
        <div class="tips" style="padding:15px;"><p style="margin: 10px 0;">{$body}</p></div>
        <div class="time" style="text-align: right; color: #999; padding: 0 15px 15px;">{$date}</div>
        </div>
    <div style="background-color: #F5F5F5;direction: ltr;padding: 16px;margin-bottom: 6px;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;">
        <table>
            <tbody>
            <tr>
                <td style="direction: ltr;"><span
                            style="font-family: Roboto-Regular,Helvetica,Arial,sans-serif; font-size: 13px;  line-height: 1.6; color: rgba(0,0,0,0.54);">本邮件发自《{$site_name}》，由系统自动发送，请勿回复。</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
EOF;
    }
}