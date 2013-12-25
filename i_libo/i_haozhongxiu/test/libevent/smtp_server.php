<?php
$socket = stream_socket_server("tcp://0.0.0.0:8002", $errno, $errstr);
if (!$socket) {
	echo "$errstr ($errno)<br />\n";
} else {
	while ($conn = stream_socket_accept($socket)) {
		fwrite($conn, "220 test.eyou.net [22092] ESMTP eYou MTA v8.1.0; Tue, 23 Apr 2013 19:47:30 +0800\n");
		$read = fread($conn, 1024);
		echo $read;
		if ("ehlo test.eyou.net\r\n" == $read) {
			fwrite($conn, "250-test.eyou.net\n250-SIZE 104857600\n250-AUTH=LOGIN PLAIN\n250-AUTH LOGIN PLAIN\n250-STARTTLS\n250 8BITMIME\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("auth login\r\n" == $read) {
			fwrite($conn, "334 VXNlcm5hbWU6\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("YWRtaW4=\r\n" == $read) {
			fwrite($conn, "334 UGFzc3dvcmQ6\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("ZXlvdWFkbWlu\r\n" == $read) {
			fwrite($conn, "235 authentication successful (rcpt_nums 100) (eYou MTA)\n");
		}
		$read = fread($conn, 1024);
		echo $read;
		if ("quit\r\n" == $read) {
			fwrite($conn, "221 close connection (eYou MTA)\n");
			fclose($conn);
		}
		/*
		*/
	}
	fclose($socket);
}
