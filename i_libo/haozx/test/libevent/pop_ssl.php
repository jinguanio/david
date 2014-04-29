<?php
$base =  new EventBase();

//$socket = stream_socket_client('mail.eyou.net:995', $errno, $errstr, 3,  STREAM_CLIENT_CONNECT);
$socket = stream_socket_client('172.16.100.110:110', $errno, $errstr, 3,  STREAM_CLIENT_CONNECT);

if (!$socket) {
	echo $errstr;	
}
$ctx = new EventSslContext(EventSslContext::SSLv23_CLIENT_METHOD, 
	array(
EventSslContext::OPT_VERIFY_DEPTH => 10,	
));

//$opt = EventBufferEvent::OPT_DEFER_CALLBACKS |  EventBufferEvent::OPT_CLOSE_ON_FREE;
//$buffer = EventBufferEvent::sslSocket($base, $socket, $ctx,  EventBufferEvent::SSL_CONNECTING, $opt);
$buffer = new EventBufferEvent($base, $socket, EventBufferEvent::OPT_CLOSE_ON_FREE|EventBufferEvent::OPT_DEFER_CALLBACKS);
$buffer->setCallbacks('read', 'write', 'error');
$buffer->enable(Event::READ);
$base->loop();

function read($buffer, $args)
{
	while (($buf = $buffer->getInput()->read(1024))) {
		echo $buf;
	}
	$buffer->getOutput()->add("\r\n");
	sleep(1);
}

function write($buffer, $args)
{
	echo "dsd";
	$buffer->write('user haozhongxiu' . "\r\n");
	$buffer->write('pass nmredadmin' . "\r\n");
	$buffer->write('quit' . "\r\n");
	$buffer->enable(Event::READ);
}

function error($buffer, $what, $args)
{
}
