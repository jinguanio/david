<?php
$context = new ZMQContext ();
$frontend = new ZMQSocket ($context, ZMQ::SOCKET_ROUTER);
$backend = new ZMQSocket ($context, ZMQ::SOCKET_DEALER);

$frontend->bind("tcp://127.0.0.1:5559");
$backend->bind("tcp://127.0.0.1:5560");

$poll = new ZMQPoll();
$poll->add($frontend, ZMQ::POLL_IN);
$poll->add($backend, ZMQ::POLL_IN);
$readable = $writeable = array();

while(true) {
	$events = $poll->poll($readable, $writeable);
	foreach ($readable as $socket) {
		if ($socket === $frontend) {
			while(true) {
				$message = $socket->recv();
				$more = $socket->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
				$backend->send($message, $more ? ZMQ::MODE_SNDMORE : null);
				if (!$more) { break;}	
			}
		} else if ($socket === $backend) {
			while(true) {
				$message = $socket->recv();
				$more = $socket->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
				$frontend->send($message, $more ? ZMQ::MODE_SNDMORE : null);
				if (!$more) { break;}	
			}
		}
	}	
}
