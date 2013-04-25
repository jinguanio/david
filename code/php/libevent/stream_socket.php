<?php
$fp = stream_socket_client('localhost:110', $errno, $errstr, 3,  STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
