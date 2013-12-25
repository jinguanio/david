<?php
$str = <<<ARR
array('a' => 1, 'b' => 'c');
ARR;

$fp = tmpfile();
fwrite($fp, $str);
fseek($fp, 0);
print_r(fread($fp, 1024));
echo PHP_EOL;
//exit(0);

print_r($s = serialize($str));
echo PHP_EOL;

print_r(eval(unserialize($s)));
echo PHP_EOL;

fclose($fp);

