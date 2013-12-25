<?php
$arr = [
    [
        'flag' => 'addr_port',
        'title' => '端口',
        'val' => 'http:127.0.0.1:80 https:127.0.0.1:443',
    ],
];
echo json_encode($arr) . "\n";

$str = '[ { "flag": "addr_port", "title": "\u7aef\u53e3", "val": "http:127.0.0.1:80 https:127.0.0.1:443"}]';
echo $str . "\n";
print_r(json_decode($str, true));
