#!/usr/bin/env php
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
$fork_num = 10;

function send_data()
{
    global $count;
    global $url, $port;

    $pid = posix_getpid();

    $fp = @stream_socket_client("tcp://{$url}:{$port}", $errno, $errstr, 30);
    if (!$fp) {
        exit(1);
    }

    $rand = random();
    $data = time() . "-{$rand}-{$pid}-{$count}";
    fwrite($fp, $data . "\r\n");
    lg("c: $data");
    //$ret = fgets($fp, 1024);
    fclose($fp);
    exit(0);
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
    echo './' . basename(__FILE__) . ' [r $1|k|s|h]' . PHP_EOL;
    echo <<<HELP
    r      run daemon
            $1 进程数
    k      kill daemon
    h      help
HELP;
}

function status()
{
    exec("ps -ef | grep 'php r'", $out);
    print_r(implode("\n", $out)."\n");
}

$daemon = new Daemon([ 'func' => 'send_data' ]);

if (isset($argv[1])) {
    if ('r' === $argv[1]) {
        if (isset($argv[2])) {
            $daemon->set_option('fork_num', $argv[2]);
        } else {
            $daemon->set_option('fork_num', $fork_num);
        }

        $daemon->fork_proc();
    } elseif ('k' === $argv[1]) {
        $daemon->kill_proc();
    } elseif ('s' === $argv[1]) {
        status();
    } else {
        help();
    }
} else {
    help();
}

