<?php
$base = new EventBase();
$fd = stream_socket_server('tcp://0.0.0.0:3333', $errno, $errstr, 3000);
$evt = new Event($base, $fd, Event::READ, 'evtcb', 'libo');
$evt->add();
function evtcb()
{
}

var_dump($evt);
$base->loop();

