<?php
$map = [
    '8000' => 'tcp://127.0.0.1',
    //'8001' => 'tcp://127.0.0.1',
];

$base = new EventBase();
$evt = [];

foreach ($map as $port => $host) {
    $fd = stream_socket_client("$host:$port", $errno, $errstr, 3);
    $evt[$port] = new Event($base, $fd, Event::WRITE, 'evtcb', array($base, $port));
    $evt[$port]->add();
}

$base->loop();

function evtcb($fd, $what, $args)
{
    $base = $args[0];
    $port = $args[1];

    $bev = new EventBufferEvent($base, $fd, 
        EventBufferEvent::OPT_CLOSE_ON_FREE,
        'readcb', 'writecb', 'eventcb', $port);
    $bev->enable(Event::READ);
}

function readcb($bev, $port)
{
    echo "$port\n";
    $data = $bev->read(1024);
    echo "$data";
}

function writecb($bev, $port)
{
    var_dump($bev->write('hello'));
}

function eventcb($bev, $events, $port)
{
    //echo "$events\n";
}

/*
$socket = stream_socket_client('tcp://127.0.0.1:8000', $errno, $errstr);
$data = fread($socket, 1024);
echo "$data";
 */

