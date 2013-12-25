<?php
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
	echo "$errstr ($errno)<br />\n";
} else {
	while ($conn = stream_socket_accept($socket)) {
		fwrite($conn, "+OK POP3 server starting on test.eyou.net (eYou MUA v8.1.0.2)--1\n");
		$read = fread($conn, 1024);
		echo $read;
		if ("user admin@test.eyou.net\r\n" == $read) {
			fwrite($conn, "+OK (eYou MUA)--1\n");
		}
		sleep(4);
		$read = fread($conn, 1024);
		echo $read;
		if ("pass eyouadmin\r\n" == $read) {
			fwrite($conn, "+OK authorization succeeded (eYou MUA)--1\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("quit\r\n" == $read) {
			fwrite($conn, "+OK bye (eYou MUA)--1\n");
			fclose($conn);
		}
	}
	fclose($socket);
}
