<?php
$context = new ZMQContext();
$subscriber = new ZMQSocket($context, ZMQ::SOCKET_SUB);

echo "Collecting updates from weather server....", PHP_EOL;
$subscriber->connect('tcp://127.0.0.1:5556');

$filter = $_SERVER['argc'] > 1 ? $_SERVER['argv'][1] : '00111';
$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $filter);

$total_temp = 0;
for($i = 0; $i < 100; $i++) {
	$string = $subscriber->recv();
	sscanf($string, "%d %d %d", $zipcode, $temperature, $relhumidity);
	echo $temperature, PHP_EOL;
	$total_temp += $temperature;	
}

printf("Average temperature for zipcode '%s' was %dF\n", $filter, (int)($total_temp / $i));
