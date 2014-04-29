<?php
$context = new ZMQContext();
$publisher = $context->getSocket(ZMQ::SOCKET_PUB);
$publisher->bind("tcp://127.0.0.1:5556");

while(true) {
	$zipcode = mt_rand(0, 500);
	$temperature = mt_rand(-80, 135);
	$relhumidity = mt_rand(10, 60);
	
	$update = sprintf("%05d %d %d", $zipcode, $temperature, $relhumidity);
	//echo $update, PHP_EOL;
	$publisher->send($update);	
	usleep(300);
}
