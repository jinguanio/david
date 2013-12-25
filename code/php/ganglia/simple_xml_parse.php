<?php
$path = '/tmp/libo/gangliaxml_?filter=summary';

$xml = new SimpleXMLElement($path, null, true);
//print_r($xml->attributes());
//print_r($xml->getName());
//print_r((array) $xml->children());
foreach ($xml as $k => $v) {
    var_dump($k, $v);
}
