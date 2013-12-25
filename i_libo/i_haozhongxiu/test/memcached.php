<?php
$server = '127.0.0.1:11211';

$timeout = 1;
$server_info = explode(':', $server);
$fp = fsockopen($server_info[0], $server_info[1], $errno, $errstri, $timeout);
if (!$fp) {
	throw new exception($errstr, $errno);
}   

fwrite($fp, 'stats' . "\r\n");

$data = array();
while (!feof($fp)) {
	$buffer = fgets($fp, 4096);
	if (strpos($buffer,"END\r\n")!==false){ // stat says end
		break;
	}
	if (strpos($buffer,"DELETED\r\n")!==false || strpos($buffer,"NOT_FOUND\r\n")!==false){ // delete says these
		break;
	}
	if (strpos($buffer,"OK\r\n")!==false){ // flush_all says ok
		break;
	}

	$buffer = explode(' ', trim($buffer));
	
	if ($buffer[0] === 'STAT' && isset($buffer[1]) && isset($buffer[2])) {
		$data[$buffer[1]] = $buffer[2];	
	} 
}
var_dump($data);
fclose($fp);

