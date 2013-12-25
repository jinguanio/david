<?php
error_reporting(E_ALL);

$com1 = '/tmp/dd';
//$com1 = '/home/libo/dd';
$com2 = '/usr/local/esop/agent/etc/etm_monitor.xml';

$cont1 = file_get_contents($com1);
$cont2 = file_get_contents($com2);
$xml = new SimpleXMLElement($cont2);
$collection_group = $xml->collection_groups->collection_group;//->collection_group[0]->metric->attributes();

$keys = [];
$err = [];
foreach ($collection_group as $kg => $vg) {
    foreach ($vg->metric as $km => $vm) {
        foreach ($vm->attributes() as $kt => $vt) {
            if (in_array($kt, [ 'value_threshold', 'title'])) {
                continue;
            }
            $keys[] = $vt->__toString();
            if (false === strpos($cont1, $vt->__toString())) {
                $err[] = $vt;
            }
        }
    }
}

echo "total: " . count($keys) . PHP_EOL;
if (0 !== count($err)) {
    echo "error: " . implode(', ', $err) . PHP_EOL;
} else {
    echo "error: 0" . PHP_EOL;
}

