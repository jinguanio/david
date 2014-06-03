<?php
require_once __DIR__ . '/../../daemon/daemon.php';

function lg($msg, $line)
{
    $msg = date('c') . $msg . "\n";
    file_put_contents('/tmp/a', $msg, FILE_APPEND | LOCK_EX);
}

function send() 
{
    $ok = false;
    while (1) {
        if ($ok) {
            continue;
        }

        $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

        $client->on("connect", function(swoole_client $cli) {
            $data = microtime(true);
            lg("Send: {$data}", __LINE__);
            $cli->send($data);
        });
        $client->on("receive", function(swoole_client $cli, $data){
            lg("Received: {$data}", __LINE__);
            $cli->close();
        });
        $client->on("error", function(swoole_client $cli){
            $cli->close();
            lg('conn close', __LINE__);
            exit(0);
        });
        $client->on("close", function(swoole_client $cli){
            lg('conn close', __LINE__);
        });

        $client->connect('127.0.0.1', 8001, 0.5);
        lg('create conn succ', __LINE__);

        $ok = true;
    }
}

if (isset($argv[1])) {
    $daemon = new Daemon([
        'func' => 'send',
        'fork_num' => 5, 
    ]);

    switch ($argv[1]) {
    case 'r':
        $daemon->run();
        break;

    case 'k':
        $daemon->stop();
        break;

    case 's':
        $daemon->status();
        break;
    }
}

