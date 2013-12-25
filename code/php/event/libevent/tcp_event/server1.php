<?php
error_reporting(true);

$map = [
    '8000' => 'tcp://0.0.0.0',
    '8001' => 'tcp://0.0.0.0',
];

$base = new EventBase();

/*
$fd = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
$evt = new Event($base, $fd, Event::READ | Event::WRITE | Event::PERSIST, function($fd, $what) {
    echo "$what\n";
    while ($conn = stream_socket_accept($fd)) {
        fwrite($conn, 'The local time is ' . date('c') . "\n");
        fclose($conn);
    }
    fclose($socket);
});
$evt->add();
$base->loop();
 */

/*
$bev = new EventBufferEvent($base, $fd, 
    EventBufferEvent::OPT_CLOSE_ON_FREE,
    null, null, null);
$bev->enable(Event::WRITE);
$bev->write("hello 8000 port\n");
 */


/*
$evt = [];

foreach ($map as $port => $host) {
    $fd = stream_socket_server("$host:$port", $errno, $errstr);
    $evt[$port] = new Event($base, $fd, Event::READ | Event::PERSIST, 'evtcb', array($base, $port));
    $evt[$port]->add();
}

$base->loop();

function evtcb($fd, $what, $args)
{ 
    $base = $args[0];
    $port = $args[1];

    $bev = new EventBufferEvent($base, $fd, 
        EventBufferEvent::OPT_CLOSE_ON_FREE,
        'readcb', 'writecb', 'eventcb', $fd);
    $bev->enable(Event::READ);
    echo 2;
} 

function readcb($bev, $port)
{
    echo 1;
    while ($conn = stream_socket_accept($fd)) {
        fwrite($conn, 'The local time is ' . date('c') . "\n");
        fclose($conn);
    }

    $bev->enable(Event::READ);
}

function writecb($bev, $fd)
{
    echo 3;
}

function eventcb($bev, $events, $port)
{
    //echo "$events\n";
}
 */

$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);

while ($conn = stream_socket_accept($socket, 3000)) {
    fwrite($conn, 'The local time is ' . date('c') . "\n");
    fclose($conn);
}
fclose($socket);

