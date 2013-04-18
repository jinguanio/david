<?php
$path = '/tmp/libo/gangliaxml_?filter=summary';

// method1
$t1 = microtime(true);
function xml_start($parser, $name, $attr) {
    echo "$name: ", var_export($attr, true);
}

function xml_end($parser, $name) {
}

$parser = xml_parser_create();
xml_set_element_handler($parser, 'xml_start', 'xml_end');
$fp = fopen($path, 'r');
while (!feof($fp)) {
    $data = fread($fp, 16*1024);
    xml_parse($parser, $data, feof($fp));
}
fclose($fp);
echo "1: ", microtime(true)-$t1, "\n";

// method2
$t1 = microtime(true);
function parse($path) {
    return parseHelper(new SimpleXmlIterator($path, null, true));
}
function parseHelper($iter) {
    foreach($iter as $key => $val)
        if ($iter->hasChildren()) {
            $arr[$key][] = call_user_func (__FUNCTION__, $val);
        } else {
            $arr[$key][] = (array) $val->attributes();
        }
    return $arr;
}

$data = parse($path);
//var_export($data);
echo "2: ", microtime(true)-$t1, "\n";

