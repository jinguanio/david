<?php
/*
 * 在当前目录设置 http 服务器
 * 如：php -S 0.0.0.0:3333
 */
error_reporting(E_ALL);

session_name('libo');
session_save_path('/tmp/sess');

session_start();
$_SESSION['file'] = __FILE__;
session_write_close();

$request = new http\Client\Request(
    'GET',
    'http://172.16.100.110:3333/serv.php?id=' . session_id(),
    [ 
        'User-Agent' => 'My Client/1.0',
        'Cookie' => session_name() . '=' . session_id(),
    ]
);
$request->setOptions([ 
    'connecttimeout' => 1,
    'timeout' => 3,
]);
$client = new http\Client;
$client->enqueue($request)->send();
$response = $client->getResponse($request);

echo "[Request]\n";
print_r($request->getHeaders());
echo "\n";
print_r($request->getRequestUrl());
echo "\n";
echo "\n[Response]\n";
print_r($response->getHeaders());
echo "\n";
echo $response->getBody()->toString();

