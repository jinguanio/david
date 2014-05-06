<?php
define('EYOUM_EXEC_SELF', true);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_redis.class.php';

$redis = em_redis::connection_singleton();

// del key
$redis->set('key1', 'val1');
echo "del key1: ";
var_dump($redis->del('key1'));
echo "\ndel key1: ";
var_dump($redis->del('key1'));
echo "\ndel key2: ";
var_dump($redis->del('key2'));
echo "\n";

// hash del
$redis->delete('h');
$redis->delete('k');
$redis->hSet('h', 'a', 'x');
var_dump($redis->hDel('h', 'a'));
var_dump($redis->hDel('k', 'c'));
exit;

//$redis->rPush('k1', 'v1');
//$redis->rPush('k1', 'v2');
//$redis->rPush('k1', 'v3');


try {
    ini_set('default_socket_timeout', 0.1);
    print_r(ini_get('default_socket_timeout'));

    do {
        $ret = $redis->blPop('k1', 0);
        print_r($ret);
    } while(1);
    //print_r($redis->lPop('k1'));
} catch (Exception $e) {
    var_dump($e);
}

