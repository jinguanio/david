<?php
$path = '../../tmp/gangliaxml_?filter=summary';
$data = file_get_contents($path);
$len = strlen($data);
$c = 1;

echo round($len/1024/1024*$c, 1), " MB", PHP_EOL;

/*
$t1 = microtime(true);
for ($i = 0; $i < $c; $i++) {
    $ret = array();
    $xml = new SimpleXMLElement($data);

    foreach ($xml->GRID->METRICS as $k => $v) {
        $ret[] = (string) $v->attributes()['NAME'];
    }

    unset($xml);
}
$t2 = microtime(true);
echo $t2-$t1, " Sec.\n";
//print_r($ret);
 */

//sleep(5);

$k_len = strlen('METRICS NAME="');
$xml = $data;
$t1 = microtime(true);
for ($i = 0; $i < $c; $i++) {
    $ret = array();
    $start = 0;
    $j = 0;

    while (1) {
        $start = strpos($xml, 'METRICS NAME="');
        if (false === $start) {
            break;
        }
        $name_start = $start + $k_len;

        $end = strpos($xml, '"', $name_start);
        if (false === $end) {
            break;
        }
        $name_len = $end - $name_start;

        $ret[] = substr($xml, $name_start, $name_len);
        $j++;

        $xml = substr($xml, $end+1);

        //sleep(10);
        if (2021 === $j) {
            break;
        }
    }
}
$t2 = microtime(true);
echo $t2-$t1, " Sec.\n";
//print_r($ret);

/*
$ret = array();
$t3 = microtime(true);
for ($i = 0; $i < $c; $i++) {
    $xml = new SimpleXMLIterator($data);
    foreach ($xml->GRID->CLUSTER as $k => $v) {
        $ret = (string) $v->attributes()['NAME'];
    }

    unset($xml);
}
$t4 = microtime(true);
//print_r($ret);
echo $t4-$t3, " Sec.\n";
 */

