<?php

$fp = fopen('http://172.16.100.114:8091/server-status?auto', 'r');

$prefix_name = 'ap_';
$prefix_name_ssl = 'apssl_';

$info = array();
if ($fp) {
	while (!feof($fp)) {
		$buffer = fgets($fp, 4096);
		//$info[] = $buffer;
		$info[] = strip_tags($buffer);
	}
	fclose($fp);
}
print_r($info);
