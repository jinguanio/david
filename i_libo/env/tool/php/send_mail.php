#!/usr/local/eyou/devmail/opt/bin/php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 发送邮件工具
 * 
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 */

/**
 * require
 */
require_once 'Mail.php';

// {{{ function send_mail()

/**
 * 发送邮件 
 * 
 * @param string $from 发件人
 * @param string $to 收件人
 * @param string $subject 主题
 * @param string $body 邮件体
 * @return void
 */
function send_mail($from = null, $to = null, $subject = null, $body = null)
{
    //smtp设置
    $host     = 'mail.eyou.net';
    $username = 'libo@eyou.net';
    $password = '535478';

    //发件人
    if (empty($from)) {
        $from    = 'mailer';
    }

    //收件人
    if (empty($to)) {
        $to = 'libo@eyou.net';
    }

    //主题
    if (empty($subject)) {
        $subject = 'test';
    } else {
        $subject = encode_msg($subject);
    }

    //邮件体
    if (empty($body)) {
        $body = 'hello world';
    } else {
        //$body = file_get_contents($body);
        $body = $body;
    }

    $headers = 
        array (
            'From'    => $from,
            'To'      => $to,
            'Subject' => $subject,
            'Date'    => date('r'),
            'Content-Type' => 'text/plain; charset="GB2312"',
        );

    $smtp = Mail::factory('smtp',
                array (
                    'host' => $host,
                    'auth' => true,
                    'username' => $username,
                    'password' => $password
                )
            );

    $smtp->send($to, $headers, $body);
}

// }}}
// {{{ function encode_msg()

/**
 * 信息编码 
 * 
 * @param string $str 待编码的字符
 * @return string
 */
function encode_msg($str)
{
    $preferences = array(
            'input-charset' => 'UTF-8',
            'output-charset' => 'GBK',
            'scheme' => 'B',
            );

    return substr(@iconv_mime_encode('', $str, $preferences), 2);
}

// }}}
// {{{ function command_args()

/**
 * 处理命令行参数 
 * 
 * @param array $argv 命令行参数数组
 * @param int $argc 参数个数
 * @return array
 */
function command_args($argv, $argc)
{
    $allow = 
        array(
            '-from' => true,        
            '-to' => true,        
            '-subject' => true,        
            '-body' => true,        
        );
    
    $command = array();
    for ($j = 1; $j < $argc;) {
        if (isset($allow[$argv[$j]])) {
            $command[$argv[$j]] = $argv[$j+1];
            $j++;
        }
        $j++;
    }

    return $command;
}

// }}}

/*
$command = command_args($argv, $argc);
send_mail($command['-from'], $command['-to'], $command['-subject'], $command['-body']);
*/

$contents = file_get_contents('http://www.360buy.com/product/193508.html');
$contents = @iconv('gbk', 'utf-8', $contents);

if (false !== strpos($contents, '北京仓现货')) {
    send_mail('JD', 'libo@eyou.net', '快去京东可以购买了', 'http://www.360buy.com/product/193508.html');
} 

