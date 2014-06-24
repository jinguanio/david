<?php
error_reporting(E_ALL);

$parter_id = 'ab7d95a0865cdbb215dbaf1ba2676ce3';
$host_name = '2824478057__test__test.com';
$k = "ma.post.{$host_name}";
$count = 130;

$arr = [
    'action' => 'postlog_list',
    'params' => [
        'columns' => [ 'plugin_name', 'serial_id' ],
        'is_count' => true,
        'has_count' => true,
        'is_enable' => 1,
        'limit' => ['count' => '2', 'offset' => '0'],
        'limit_page' => ['page' => 1, 'rows_count' => '3'],
        'order' => 'plugin_name desc',
        'pid' => 'ab7d95a0865cdbb215dbaf1ba2676ce3',
        'pname' => 'eyou',
        'where' => [
            'like' => [
                'plugin_name' => 'pop_svr',
                'title' => 'HTTP',
            ],
            'eq' => [
                'job_time' => '1393330429',
                'platform_time' => '1397143915',
                'job_id' => '1ce2a6c1ce20328542d6fadb433f4bb1',
                'post_level' => 'warn',
                'info_type' => 'file',
                'result' => '0',
                'serial_id' => '2524662888',
            ],
            'nin' => [
                'plugin_name' => [ 'disk_fs', 'cpu_usage' ],
            ],
            'le' => [
                'err_num' => 1,
            ],
            'ge' => [
                'err_num' => 1,
            ],
            'in' => [
                'serial_id' => ['614b1c188295b488c030163f1d27d6e7'],
                'job_id' => ['1ce2a6c1ce20328542d6fadb433f4bb1'],
            ]
        ],
    ]
];

$t1 = microtime(true);
$redis = new Redis();
$redis->connect('localhost', '6379');
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
$t2 = microtime(true);
echo "create conn: " . ($t2-$t1) . "\n";

function redis() {
    global $k, $redis;

    //$t1 = microtime(true);
    //$t2 = microtime(true);
    $ret = $redis->hGetAll($k);
    //$t3 = microtime(true);
    //echo 'create conn: ' . $t2-$t1 . "\n";
    //echo 'get data: ' $t3-$t2 . "\n";
}

function json()
{
    global $arr;

    $json = json_encode($arr);
}

function redis_2($i)
{
    global $redis, $arr;

    $k = 'libo';
    $f = $k . $i; 

    return $redis->hSet($k, $f, $arr);
}

function redis_21($i)
{
    global $redis, $arr;

    $k = 'libo';
    $f = $k . $i; 

    return $redis->hGet($k, $f);
}

function redis_3($i)
{
    global $redis, $arr;

    $k = 'libo';
    $f = $k . $i . $i; 

    return $redis->hSet($k, $f, json_encode($arr));
}

function redis_31($i)
{
    global $redis, $arr;

    $k = 'libo';
    $f = $k . $i . $i; 

    return json_decode($redis->hGet($k, $f), true);
}


function performance($func)
{
    echo "========= {$func} =============\n";
    $t1 = microtime(true);
    $ret = [];
    for ($i = 0, $c = 130; $i < $c; $i++) {
        $ret[] = $func($i);
    }
    $t2 = microtime(true);
//    ob_start();
    //print_r($ret);
    //ob_clean();
    echo "total: " . ($t2-$t1) . "\n";
}

//performance('redis');
//performance('json');

performance('redis_2');
//performance('redis_3');

//performance('redis_21');
//performance('redis_31');

