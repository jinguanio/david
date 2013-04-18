<?php
$path = '/root/git/sunshine/tmp/gangliaxml_?filter=summary';
$len = strlen(file_get_contents($path));
$c = 20;
$ret = array();

echo round($len/1024/1024*$c, 1), " MB", PHP_EOL;

$t1 = microtime(true);
for ($i = 0; $i < $c; $i++) {
    $xml = new SimpleXMLElement($path, null, true);

    foreach ($xml->GRID->CLUSTER as $k => $v) {
        $ret = (string) $v->attributes()['NAME'];
    }

    unset($xml);
}
$t2 = microtime(true);
//print_r($ret);
echo $t2-$t1, " Sec.\n";

echo "delay...\n";
sleep(1);

$ret = array();
$t3 = microtime(true);
for ($i = 0; $i < $c; $i++) {
    $xml = new SimpleXMLIterator($path, null, true);
    foreach ($xml->GRID->CLUSTER as $k => $v) {
        $ret = (string) $v->attributes()['NAME'];
    }

    unset($xml);
}
$t4 = microtime(true);
//print_r($ret);
echo $t4-$t3, " Sec.\n";
