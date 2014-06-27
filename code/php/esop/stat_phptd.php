<?php
error_reporting(E_ALL);

$file = '/usr/local/eyou/toolmail/log/phptd.log';
$cont = file_get_contents($file);

preg_match_all('/current connections: (\d+)/i', $cont, $match);
rsort($match[1], SORT_NUMERIC);
var_dump($match[1]);
