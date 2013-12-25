<?php

$base = new EventBase();
$event = Event::timer($base, 'callback');
$event->add(4);
$event1 = Event::timer($base,  'callback1', "debug");
$event1->add(4);
while(1) {
	echo "\n" . time() . "start \n";
	$base->exit(1);
	$base->loop(EventBase::NO_CACHE_TIME);
//	sleep(10);
}


function callback()
{
	global $base;
	global $event;
	$event->setTimer($base, __FUNCTION__);
	$event->addTimer(3);
	echo time() . "timer1\n";	
}

function callback1($arg)
{
	var_dump($arg);
	global $base;
	global $event1;
	$event1->setTimer($base, __FUNCTION__, "rrr");
	$event1->addTimer(3);
	echo time() . "timer2\n";	
}


