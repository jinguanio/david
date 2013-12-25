<?php
require_once 'send_mail.php'

$contents = file_get_contents('http://www.360buy.com/product/193508.html');
if (false !== strpos($contents, '北京仓无货')) {
    send_mail('jingdong', 'libo@eyou.net', '快去京东可以购买了', 'http://www.360buy.com/product/193508.html');
}
