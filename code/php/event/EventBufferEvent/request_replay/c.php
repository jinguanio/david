<?php
error_reporting(E_ALL);

$read_timeout = 3;
$write_timeout = 3;
$delay = 5;

$timer = null;
$base = new EventBase();
connect_dest();

while (1) {
    $base->exit(2);
    $base->dispatch();
}

function connect_dest()
{
    global $read_timeout, $write_timeout;

    echo "connect dest...\n";
    global $base;

    $buffer = new EventBufferEvent($base, /* use internal socket */ NULL,
            EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS,
            'readcb', null, 'eventcb', $base
        );
    $buffer->enable(Event::READ);
    $buffer->setTimeouts($read_timeout, $write_timeout);
    $buffer->connect('127.0.0.1:8000');
    $buffer->write('client data, ' . time());
}

function readcb($buffer, $base)
{
    global $delay;

    echo "read running...\n";

    echo $buffer->read(1024), "\n";

    $buffer->enable(Event::WRITE);
    $buffer->setCallbacks('readcb', 'writecb', 'eventcb');
    sleep($delay);
}

function writecb($buffer, $base)
{
    echo "write running...\n";

    $buffer->write('client data, ' . time());
    $buffer->setCallbacks('readcb', null, 'eventcb');
    $buffer->enable(Event::READ);
}

function eventcb($buffer, $events, $base)
{
    echo "event running, status: $events\n";

    if ($events & EventBufferEvent::EOF) {
        echo "client EOF from bufferevent, Close.\n";
        $buffer->free();
        unset($buffer);
        retry_connect($base);
    }

    if ($events & EventBufferEvent::ERROR) {
        echo "client ERROR from bufferevent, Close.\n";
        $buffer->free();
        unset($buffer);
        retry_connect($base);
    }

    if ($events & EventBufferEvent::TIMEOUT) {
        echo "client TIMEOUT from bufferevent, Close.\n";
        $buffer->free();
        unset($buffer);
        retry_connect($base);
    }
}

function free($buffer, $base)
{
    $buffer->free();
    unset($buffer);
}

function retry_connect($base)
{
    echo "\nretry connect...\n\n";

    global $timer;

    if ($timer) {
        $timer->del();
    }

    $timer = Event::timer($base, 'connect_dest', $base);
    $timer->addTimer(1);
}

