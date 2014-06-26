<?php
error_reporting(E_ALL);

/**
 * 测试代理并发处理功能
 *
 * 需要配合 clear_test_log.sh 脚本
 * 清理 /tmp/{serv,cli}.log 下的调试日志
 * 清理 agent/toolmail 下 phptd 日志
 */

/**
 * require
 */
require_once __DIR__ . '/../daemon/daemon.php';

$count = 1; $url = 'localhost'; $port = 8548;
$fork_num = 200;
$max_send_num = 50;
$send_interval = 1;

function send_data()
{
    global $count, $max_send_num, $send_interval;
    global $url, $port;

    $pid = posix_getpid();

    while (1) {
        if ($max_send_num < $count) {
            //$count = 1;
            sleep($send_interval);
            continue;
        }

        $fp = @stream_socket_client("tcp://{$url}:{$port}", $errno, $errstr, 30);
        if (!$fp) {
            exit(1);
        }

        $rand = random();
        $data = time() . "-{$rand}-{$pid}-{$count}";
        fwrite($fp, $data . "\r\n");
        lg("c: $data");
        fgets($fp, 1024);
        fclose($fp);

        $count++;

        usleep(5000);
    }
}

function random()
{
    return md5(microtime(true) . mt_rand(0, 99999999));
}

function lg($msg)
{
    $file = '/tmp/cli';
    file_put_contents($file, $msg . "\n", FILE_APPEND | LOCK_EX);
}

function help()
{
    echo './' . basename(__FILE__) . ' [r|k|h]' . PHP_EOL;
    echo <<<HELP
    r      run daemon
    k      kill daemon
    h      help
HELP;
}

$daemon = new Daemon([
    'func' => 'send_data',
    'fork_num' => $fork_num,
    ]);

if (isset($argv[1])) {
    if ('r' === $argv[1]) {
        $daemon->fork_proc();
    } elseif ('k' === $argv[1]) {
        $daemon->kill_proc();
    } else {
        help();
    }
} else {
    help();
}

