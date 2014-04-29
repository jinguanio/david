<?php
$socket = stream_socket_server("udp://127.0.0.1:8649", $errno, $errstr, STREAM_SERVER_BIND);
if (!$socket) {
	die("$errstr ($errno)");
}

do {
	$pkt = stream_socket_recvfrom($socket, 1024, 0, $peer);
//	echo bin2hex($pkt), "\n";
} while ($pkt !== false);

?> 
