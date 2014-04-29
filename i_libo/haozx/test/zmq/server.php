<?php

$context = new ZMQContext(1);

$responder = new ZMQSocket($context, ZMQ::SOCKET_REQ);
$responder->bind("tcp://127.0.0.1:5555");

while(true) {
	echo "dsds";
	try {
	$request = $responder->recv();
	printf("Received request:[%s]\n", $request);
	} catch (ZMQSocketException $e) {
	}
	
	sleep(1);
	
	$responder->send("World");	
}

