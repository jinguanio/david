<?php
$base = new EventBase();
$fp = stream_socket_client('localhost:8003', $errno, $errstr, 1);
$event_buffer = new EventBufferEvent($base, $fp, EventBufferEvent::OPT_DEFER_CALLBACKS | EventBufferEvent::OPT_CLOSE_ON_FREE, 'ev_read', 'ev_write', 'ev_error', 1);
$event_buffer->enable(Event::WRITE | Event::READ | Event::TIMEOUT | Event::PERSIST);
$event_buffer->setTimeouts(1, 1);
$base->loop();

function ev_read($buffer)
{
	$buffer->read($read, 1024);
	echo $read;
//	$input = $buffer->getInput();
//	var_dump($input);
}

function ev_write($buffer)
{
	
}

function ev_error($buffer, $status)
{
	echo $status;
}
