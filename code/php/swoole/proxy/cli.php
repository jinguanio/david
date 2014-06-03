<?php
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

$client->on("connect", function(swoole_client $cli) {
    //echo "onConnect\n";
    $data = microtime(true);
    //sleep(30);
    echo "Send: {$data}\n";
    $cli->send($data);
});

$client->on("receive", function(swoole_client $cli, $data){
    echo "Received: $data\n";
    $cli->close();
});

$client->on("error", function(swoole_client $cli){
    exit("error\n");
});

$client->on("close", function(swoole_client $cli){
    //echo "Connection close\n";
});

$client->connect('127.0.0.1', 8001, 0.5);
//echo "connect to 127.0.0.1:8001\n";

