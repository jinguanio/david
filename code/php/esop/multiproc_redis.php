<?php
error_reporting(E_ALL);

declare(ticks = 1);

$pids = [];
$redis_key = 'libo-123';
$pid_file = '/tmp/proc.pid';

// {{{ function fork_proc()

/**
 * fork_proc 
 * 
 * @param string $func 函数名
 * @param int $fork_num fork 进程数
 * @return void
 * @throws Exception
 */
function fork_proc($func, $fork_num = 5)
{
    global $pids;

    // 忽略终端 I/O信号,STOP信号
    pcntl_signal(SIGTTOU, SIG_IGN);
    pcntl_signal(SIGTTIN, SIG_IGN);
    pcntl_signal(SIGTSTP, SIG_IGN);
    pcntl_signal(SIGHUP, SIG_IGN);
   
    $pid = pcntl_fork();
    if (0 != $pid) {
        exit(0);
    }

    write_pid();
    posix_setsid();
    chdir('/tmp');
    umask(0);
    fork_child($func, $fork_num);

    pcntl_signal(SIGUSR1, 'kill_child');

    while (1) {
        sleep(60);
    }
}

// }}}
// {{{ function fork_child()

function fork_child($func, $num)
{
    global $pids;

    for ($i = 0; $i < $num; $i++) {
        $pid = pcntl_fork();

        pcntl_signal(SIGTERM, SIG_DFL);
        pcntl_signal(SIGCHLD, SIG_DFL);

        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            $pids[] = $pid;
            pcntl_wait($status, WNOHANG); 
        } else {
            $func();
        }
    }
}

// }}}
// {{{ function write_pid()

function write_pid()
{
    global $pid_file;

    $fp = dio_open($pid_file, O_WRONLY|O_CREAT, 0644);  
    dio_truncate($fp, 0);
    dio_write($fp, posix_getpid()); 
}

// }}}
// {{{ function kill_proc()

/**
 * kill_proc 
 * 
 * @return void
 * @throws Exception
 */
function kill_proc()
{
    global $pid_file;
    $ppid = file_get_contents($pid_file);
    posix_kill($ppid, SIGUSR1);
}

// }}}
// {{{ function kill_child()

function kill_child()
{
    global $pids, $pid_file;

    foreach ($pids as $p) {
        posix_kill($p, SIGTERM);
    }

    $ppid = file_get_contents($pid_file);
    posix_kill($ppid, SIGTERM);
}

// }}}

// {{{ function set_queue()

function set_queue($total = 10000)
{
    global $redis_key;

    $redis = conn_redis();

    for ($i = 1; $i <= $total; $i++) {
        $redis->rPush($redis_key, $i);
    }

    lg();
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
            lg('val: ' . var_export($ret[1], true));
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

if (isset($argv[1])) {
    if ('i' === $argv[1]) { // insert redis
        $t1 = microtime(true);
        set_queue(1000000);
        $t2 = microtime(true);
        echo 'eclipse: ' . ($t2-$t1) . "\n";
    } elseif ('r' === $argv[1]) { // run multi-process
        fork_proc('get_queue', 100);
    } elseif ('k' === $argv[1]) { // kill multi-process
        kill_proc();
    } else {
        help();
    }
} else {
    help();
}

