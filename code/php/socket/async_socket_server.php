<?php
$file = 'helper/log';
foreach ($_GET as $key => $v) {
    $fp = fopen($file, 'a+');
    fwrite($fp, "date: " . date('c') . ", {$key} => {$v}" . PHP_EOL);
    fclose($fp);
    sleep(10);
}

