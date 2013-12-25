<?php
$fp = stream_socket_client("tcp://172.16.100.128:8000", $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, "adsfasdf\r\n");
    echo stream_get_contents($fp);
    fclose($fp);
}

