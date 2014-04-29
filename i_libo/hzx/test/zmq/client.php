<?php

$context = new ZMQContext(1);

echo "connect to hello world server .... \n";

$requester = new ZMQSocket($context, ZMQ::SOCKET_REQ);

$requester->connect("tcp://localhost:5555");

for ($i = 0; $i < 10; $i++) {
	printf("Sending request %d...\n", $i);
	
	$requester->send("Hello");
	
	$reply = $requester->recv();
	
	printf("Recevied reply %d:[%s]\n", $i, $reply);	
}
