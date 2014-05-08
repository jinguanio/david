<?php
error_reporting(E_ALL);

$curr = time();
$during = '10sec';
$during = '10min';
$during = '1hour';
$during = '1day';
$during = '30day';
$during = '1week';
$during = '10';
$time = strtotime('+'.$during);
echo "curr: ", date('Y-m-d H:i:s', $curr), "\n";
echo "time: ", date('Y-m-d H:i:s', $time), "\n";

