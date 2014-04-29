<?php

$fp = stream_socket_client('127.0.0.1:8001', $errno, $errstr, 3);
stream_set_timeout($fp, 2);
if (!$fp) {
	echo "$errstr ($errno)<br />\n";
} else {
		echo fgets($fp, 1024);
	 $info = stream_get_meta_data($fp);
	 var_dump($info);
	fclose($fp);
}
?> 


