<?php
$socket = stream_socket_server("tcp://0.0.0.0:8001", $errno, $errstr);
if (!$socket) {
	echo "$errstr ($errno)<br />\n";
} else {
	//sleep(5);
	while ($conn = stream_socket_accept($socket)) {
		fwrite($conn, "+OK POP3 server starting on test.eyou.net (eYou MUA v8.1.0.2)--1\n");
		$read = fread($conn, 1024);
		if ("user admin@test.eyou.net" == $read) {
			fwrite($conn, "+OK (eYou MUA)--1\n");
		}
		$read = fread($conn, 1024);
		if ("pass eyouadmin" == $read) {
			fwrite($conn, "+OK authorization succeeded (eYou MUA)--1\n");
		}
		$read = fread($conn, 1024);
		if ("quit" == $read) {
			fwrite($conn, "+OK bye (eYou MUA)--1\n");
			fclose($conn);
		}
	}
	fclose($socket);
}
