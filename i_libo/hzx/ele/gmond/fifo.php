<?php
declare(ticks = 1);
$event_base = new EventBase();
$file_name = '/usr/local/eyou/toolmail/run/pgmond.fifo';
$pid = pcntl_fork();	
if ($pid) {
	$fp = fopen($file_name, 'r');
	stream_set_blocking($fp, false);
	//while(1) {
	//	var_dump(fread($fp, 1024));
	//	sleep(1);	
	//}
	$event = new EventBufferEvent($event_base, $fp, EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS,'callback', null, null);
	$event->enable(Event::READ);

	$event_base->loop();
	echo "debug";
//	$base = event_base_new();
//	$event = event_new();
//
//	event_set($event, $fp, EV_READ, "callback");
//
//	event_base_set($event, $base);
//	event_add($event);
//	event_base_loop($base);
	while(($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
			
	}

	while(1) {
		usleep(100);	
	}
} elseif ($pid == 0) {
	var_dump(posix_mkfifo($file_name, 0644));	
	$fp = fopen($file_name, 'w+');
	while(1) {
		var_dump(fwrite($fp, 'test' . time()));
		sleep(3);
		//fclose($fp);
	}
} else {
	exit(1);	
}

function callback($buf, $what)
{
	var_dump("eee");
	var_dump($buf->read(1024));
}
