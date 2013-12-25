<?php
$rrd_path = '/usr/local/eyou/toolmail/data/rrds/eYouMail/lan-100.114/';
$rrd_data = 'harddisk_use__sda1.rrd';
$rrd = $rrd_path . $rrd_data;
$rrdtool = '/usr/local/eyou/toolmail/opt/bin/rrdtool';
$cf = 'AVERAGE';
/*
$cf = 'MAX'; // ERROR: the RRD does not contain an RRA matching the chosen CF
$cf = 'MIN'; // ERROR: the RRD does not contain an RRA matching the chosen CF
$cf = 'LAST'; // ERROR: the RRD does not contain an RRA matching the chosen CF
 */

$end = time();
$start = $end - 86400*1;

$cmd = "{$rrdtool} fetch {$rrd} {$cf} -s {$start} -e {$end}";
echo "$cmd\n";
exec($cmd, $out, $res);
//print_r($out);

$ret = [];
foreach ($out as $item) {
    $item = trim($item);

    if (!empty($item) && false !== strpos($item, ':')) {
        list($time, $data) = explode(':', $item);
        $time = date('Y-m-d H:i:s', trim($time));
        $data = trim($data);
        $ret[] = "$time : $data"; 
    } else {
        $ret[] = $item;
    }
}

print_r($ret);

