<?php
error_reporting(E_ALL);

$base = new EventBase();
$buffer = new EventBufferEvent($base, /* use internal socket */ NULL,
        EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS,
        'readcb', 'writecb', 'eventcb', $base
    );
$buffer->enable(Event::READ);
$buffer->setTimeouts(3, 3);
$buffer->connect('127.0.0.1:8000');
$base->dispatch();

function readcb($buffer, $base)
{
    echo "read running...\n";

    $data = '';
    while ($read = $buffer->read(1024)) {
        $data .= $read;
    }
    echo 'read input buffer, data: ', $data, PHP_EOL;

    $body = "client.hello";
    $length = strlen($body);
    $buffer->write($body);
}

function writecb($buffer, $base)
{
    echo "write running...\n";

    free($buffer, $base);
    echo "close client.\n";
}

function eventcb($buffer, $events, $base)
{
    echo "event running, status: $events\n";

    if ($events & (EventBufferEvent::EOF | EventBufferEvent::ERROR)) {
        echo "client EOF or ERROR from bufferevent\n";
        $buffer->free();
        unset($buffer);
    }
}

function free($buffer, $base)
{
    $buffer->free();
    unset($buffer);
}


