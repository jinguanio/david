#!/usr/local/eyou/toolmail/opt/bin/php
<?php
$path = '/usr/local/eyou/toolmail/data/rrds/esop.eyou.net/dev/';
$rrd_file = 'load_fifteen.rrd';
$rrd_file = 'net_rx_bytes__lo.rrd';

$opt = [
    'AVERAGE',
    '-r', '600',
    '-s', '-1h',
    ];
var_dump($opt);
$ret = rrd_fetch($path.$rrd_file, $opt);
var_export($ret);
exit;

function _d($d)
{
    return date('Y-m-d H:i:s', $d);
}

$ret['start'] = _d($ret['start']);
$ret['end'] = _d($ret['end']);

$arr = [];
foreach ($ret['data']['sum'] as $k => $v) {
    $arr[_d($k)] = $v;
}
$ret['data']['sum'] = $arr;

print_r($ret);

