<?php
$base = new EventBase();

$fp = stream_socket_client('localhost:8000', 
    $errno, $errstr, 3,
    STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT
);
$evt = new Event($base, $fp, 
    Event::READ | Event::WRITE,
    'evtcb'
);
$evt->add();

$base->loop();	
 
function evtcb($fd, $events)
{
    dial_client($fd);
}

function dial_client($fd)
{
	$read = fread($fd, 1024);
    echo $read;
	if (0 === strpos($read, trim('+OK POP3'))) {
		fwrite($fd, "user admin@test.eyou.net\r\n\r\n");
	}

	$read = fread($fd, 1024);
    echo $read;
	if (0 === strpos($read, trim('+OK (eYou MUA)'))) {
		fwrite($fd, "pass eyouadmin\r\n\r\n");
	}

	$read = fread($fd, 1024);
    echo $read;
	if (0 === strpos($read, trim('+OK auth'))) {
		fwrite($fd, "quit\r\n\r\n");
	}

	$read = fread($fd, 1024);
    echo $read;
    //echo "========================\n";
}


