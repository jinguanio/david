<?php
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
	echo "$errstr ($errno)<br />\n";
} else {
	while ($conn = stream_socket_accept($socket)) {
		fwrite($conn, "+OK POP3 server starting on test.eyou.net (eYou MUA v8.1.0.2)\n");
		$read = fread($conn, 1024);
		if ("user admin@test.eyou.net" == $read) {
			fwrite($conn, "+OK (eYou MUA)\n");
		}
		$read = fread($conn, 1024);
		if ("pass eyouadmin" == $read) {
			fwrite($conn, "+OK authorization succeeded (eYou MUA)\n");
		}
		$read = fread($conn, 1024);
		if ("quit" == $read) {
//			sleep(10);
			fwrite($conn, "+OK bye (eYou MUA)\n");
			fclose($conn);
		}
	}
	fclose($socket);
}
