<?php
$socket = stream_socket_server("tcp://0.0.0.0:8001", $errno, $errstr);
if (!$socket) {
	echo "$errstr ($errno)<br />\n";
} else {
	while ($conn = stream_socket_accept($socket)) {
		fwrite($conn, "* OK IMAP4rev1 server starting on test.eyou.net (eYou MUA v8.1.0.2)\n");
		$read = fread($conn, 1024);
		echo $read;
		if ("gmond login admin@test.eyou.net eyouadmin\r\n" == $read) {
			fwrite($conn, "gmond OK LOGIN completed. (eYou MUA)\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("gmond list \"~/Mail/\" \"%\"\r\n" == $read) {
			fwrite($conn, "gmond OK LOGIN completed. (eYou MUA)\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("gmond logout\r\n" == $read) {
			fwrite($conn, "* BYE IMAP4rev1 server shutting down\ngmond OK LOGOUT completed. (eYou MUA)\n");
			fclose($conn);
		}
	}
	fclose($socket);
}
