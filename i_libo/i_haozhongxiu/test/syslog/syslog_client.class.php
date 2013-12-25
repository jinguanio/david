<?php
$fp = stream_socket_client("127.0.0.1:5554", $errno, $errstr, 30);
if (!$fp) {
	echo "$errstr ($errno)<br />\n";
} else {
	while(true) {
		$time = time();
		fwrite($fp, "<174> test...............$time\n");
	}
	fclose($fp);
}
