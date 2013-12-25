<?php
$base = event_base_new();
$event = event_new();
$fp = stream_socket_client('localhost:8000', $errno, $errstr, 3);
event_set($event, $fp, EV_READ | EV_PERSIST, "ev_read", array($event, $base));
// set event base
event_base_set($event, $base);
// enable event
event_add($event);

$event_1 = event_new();
$fp_1 = stream_socket_client('localhost:8001', $errno, $errstr, 3);
event_set($event_1, $fp_1, EV_READ | EV_PERSIST, "ev_read", array($event_1, $base));
// set event base
event_base_set($event_1, $base);
// enable event
event_add($event_1);

while(1) {
	event_base_loopexit($base, 1000000);	
	event_base_loop($base);	
	echo "debug";
}
 
function ev_read($fd, $events, $arg)
{
	$read = fread($fd, 1024);
	if (0 === strpos($read, trim('+OK POP3'))) {
		fwrite($fd, "user admin@test.eyou.net");
	}
	if (0 === strpos($read, trim('+OK (eYou MUA)'))) {
		fwrite($fd, "pass eyouadmin");
	}

	if (0 === strpos($read, trim('+OK auth'))) {
		fwrite($fd, "quit");
	}
	echo $read;
}

function ev_error($buffer) 
{
	
}
