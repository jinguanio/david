<?php
error_reporting(E_ALL);
require_once __DIR__ . '/../daemon/daemon.php';

$redis_key = 'libo-123';

// {{{ function set_queue()

function set_queue($total = 10000)
{
    global $redis_key;

    $redis = conn_redis();

    for ($i = 1; $i <= $total; $i++) {
        $redis->rPush($redis_key, $i);
    }

    //lg();
    echo "insert redis queue succ\n";
}

// }}}
// {{{ function get_queue()

function get_queue()
{
    global $redis_key;

    $redis = conn_redis();

    while (1) {
        $ret = $redis->blPop($redis_key, 30);
        if (isset($ret[1])) {
            //lg("[" . posix_getpid() . '] ' . var_export($ret[1], true));
            lg($ret[1]);
        }
        usleep(5000);
    }
}

// }}}
// {{{ function conn_redis()

function conn_redis()
{
    $redis = new Redis();
    $redis->connect('localhost', '6379');
    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    return $redis;
}

// }}}
// {{{ function lg()

function lg($msg = null)
{
    $file = '/tmp/bb';
    if (null !== $msg) {
        file_put_contents($file, $msg . "\n", FILE_APPEND | LOCK_EX);
    } else {
        unlink($file);
    }
}

// }}}
// {{{ function help()

function help()
{
    echo 'php ./' . basename(__FILE__) . " [i|r]\n";
    echo <<<HELP
    i   insert data into redis
    r   multi-process get data from redis

HELP;
}

// }}}

$daemon = new Daemon();
$daemon->set_options([
    'func' => 'get_queue',
    'fork_num' => 10,
    ]);

if (isset($argv[1])) {
    if ('i' === $argv[1]) { // insert redis
        $t1 = microtime(true);
        set_queue(10000);
        $t2 = microtime(true);
        echo 'eclipse: ' . ($t2-$t1) . "\n";
    } elseif ('r' === $argv[1]) { // run multi-process
        $daemon->fork_proc();
    } elseif ('k' === $argv[1]) { // kill multi-process
        $daemon->kill_proc();
    } else {
        help();
    }
} else {
    help();
}

