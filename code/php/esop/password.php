<?php
define('EYOUM_EXEC_SELF', true);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_password.class.php';

$passwd = em_password::concat_public_label('aaaaa123');
$pwd = new em_password($passwd);
$ret = $pwd->encode();
var_dump($ret);
exit;

//$ret = "{h-md5-b}4QrcOUm6Wau+VuBX8g+IPg==";
$pwd = new em_password($passwd);
// 返回 0 代表正确，其他值为错误。
var_dump($pwd->compare($ret));

