<?php
$len = 16*1024;
$ip = '127.0.0.1';
$port = 8652;
$cmd = array(
    0 => '/',
    1 => '/?filter=summary',
    2 => '/eYouMail',
    3 => '/eYouMail?filter=summary',
    4 => '/eYouMail/lan-100.114',
);

foreach ($cmd as $key => $val) {
    $fp = fsockopen($ip, $port, $errno, $errstr, 3);
    fputs($fp, $val . "\n");

    $data = '';
    $i = 0;
    while(!feof($fp)) {
        $i++;
        $data .= fread($fp, $len);
    }

    $suffix = trim($val, '/');
    if (!empty($suffix)) {
        $suffix = str_replace('/', '-', $suffix);
    } else {
        $suffix = 'root';
    }
    file_put_contents("/tmp/gangliaxml_{$suffix}", $data);

    echo "{$val}: ";
    print_r($i);
    echo "\n";
}

