<?php
define('EYOUM_EXEC_SELF', true);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_redis.class.php';

$redis = em_redis::connection_singleton();

function test_use()
{
    global $redis;

    $timeout = 4;

    $redis->hset('k', 'f', 'v');
    $redis->expire('k', $timeout);
    //sleep(1);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    var_dump($redis->hgetall('k'));
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "delay 2 sec...", "\n";
    sleep(2);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "\n\n";

    $redis->hset('k', 'f', 'v');
    $redis->expire('k', $timeout);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    var_dump($redis->hgetall('k'));
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "\n\n";

    /*
    $redis->set('k', 'f');
    $redis->expire('k', $timeout);
    //sleep(1);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    var_dump($redis->get('k'));
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "delay 2 sec...", "\n";
    sleep(2);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "\n\n";

    $redis->set('k', 'f');
    //$redis->expire('k', $timeout);
    echo 'ttl: ', $redis->ttl('k'), "\n";
    var_dump($redis->get('k'));
    echo 'ttl: ', $redis->ttl('k'), "\n";
    echo "\n\n";
     */
}

function test_speed()
{
    global $redis;
    $timeout = 4;

    echo "start...\n";
    $t1 = microtime(true);

    $count = 100000;
    for ($i = 0; $i < $count; $i++) {
        $redis->hset('k'.$i, 'f'.$i, 'v');
        $redis->expire('k', $timeout);
    }
    sleep(2);

    for ($i = 0; $i < $count; $i++) {
        $redis->hget('k'.$i, 'f'.$i);
    }
    echo (microtime(true)-$t1) . " sec";
}

// 功能测试
//test_use();

// 性能测试
test_speed();

