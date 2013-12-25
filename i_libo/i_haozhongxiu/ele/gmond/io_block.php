<?php

declare(ticks=1);

pcntl_signal(SIGALRM, 'handler_timeout', false);
pcntl_alarm(2);
$fp = stream_socket_client("tcp://127.0.0.1:8001", $errno, $errstr, 30);
if (!$fp) {
	echo "$errstr ($errno)<br />\n";
} else {
	fwrite($fp, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
	while (!feof($fp)) {
		echo fgets($fp, 1024);
	}
	fclose($fp);
}


function handler_timeout($sig)
{
	echo "debug\n";	
}
