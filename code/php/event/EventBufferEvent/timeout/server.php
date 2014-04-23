<?php
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
    echo "$errstr ($errno)<br />\n";
} else {
    while ($conn = stream_socket_accept($socket)) {
        $data = fread($conn, 1024);
        echo $data;

        fwrite($conn, 'The local time is ' . date('c') . "\n");
        fclose($conn);
    }
    fclose($socket);
}

