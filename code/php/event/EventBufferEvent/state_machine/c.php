<?php
error_reporting(E_ALL);

$crlf = "\r\n";
$base = new EventBase();
$buffer = new EventBufferEvent($base, /* use internal socket */ NULL,
        EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS,
        'readcb', null, 'eventcb', $base
    );
$buffer->enable(Event::READ);
$buffer->setTimeouts(3, 3);
$buffer->connect('127.0.0.1:8000');
$base->dispatch();

function readcb($buffer, $base)
{
    global $crlf;

    echo "read running...\n";

    $data = '';
    while ($read = $buffer->read(1024)) {
        $data .= $read;
    }
    list($cmd, $ret) = explode($crlf, $data);
    echo 'resp: ', var_export(json_decode($ret, true), true), PHP_EOL;

    switch ($cmd) {
    case 's.ehlo':
        $req = 'c.ehlo' . $crlf;
        break;

    case 's.cmd':
        $req = 'c.ok.1' . $crlf;
        break;

    case 's.ls -al':
        $req = 'c.ok.2' . $crlf;
        exec('ls -al', $out, $ret);
        $req .= json_encode($out);
        break;

    case 's.end';
        $req = 'c.ok.3' . $crlf;
        $buffer->setCallbacks(null, 'free', 'eventcb');
        break;
    }

    echo "req: " . trim($req), PHP_EOL, PHP_EOL;
    $buffer->write($req);
    $buffer->enable(Event::WRITE);
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


