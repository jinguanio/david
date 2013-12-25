<?php
error_reporting(E_ALL);

$crlf = "\r\n";
$base = new EventBase();
$listener = new EventListener($base, 'callback_accept', $base,
    EventListener::OPT_CLOSE_ON_FREE | EventListener::OPT_REUSEABLE, -1,
    "127.0.0.1:8000");
$listener->setErrorCallback('callback_accept_error');
$base->dispatch();

function callback_accept($listener, $fd, $address, $base)
{
    echo "accept running...\n";

    $buffer = new EventBufferEvent($base, $fd, EventBufferEvent::OPT_CLOSE_ON_FREE);
    $buffer->setCallbacks('callback_read', 'callback_write', 'callback_event', $base);
    $buffer->setTimeouts(3, 3);
    $buffer->enable(Event::WRITE);
}

function callback_accept_error($listener, $base)
{
    echo "accept error running...\n";

    fprintf(STDOUT, "Got an error %d (%s) on the listener. Shutting down.\n",
        EventUtil::getLastSocketErrno(),
        EventUtil::getLastSocketError());

    $base->exit();
}

function callback_read($buffer, $base)
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
    case 'c.ehlo':
        $req = 's.cmd' . $crlf;
        break;

    case 'c.ok.1':
        $req = 's.ls -al' . $crlf;
        break;

    case 'c.ok.2':
        $req = 's.end' . $crlf;
        break;

    case 'c.ok.3':
        free($buffer, $base);
        return;
        // break;
    }

    echo "req: " . trim($req), PHP_EOL, PHP_EOL;
    $buffer->write($req);
}

function callback_write($buffer, $base)
{
    global $crlf;

    echo "write running...\n";

    $req = 's.ehlo' . $crlf;
    echo "req: " . trim($req), PHP_EOL, PHP_EOL;
    $buffer->write($req);

    $buffer->setCallbacks('callback_read', null, 'callback_event', $base);
    $buffer->enable(Event::READ);
}

function callback_event($buffer, $events, $base)
{
    echo "event running, status: $events\n";

    if ($events & (EventBufferEvent::EOF | EventBufferEvent::ERROR)) {
        echo "server EOF or ERROR from bufferevent, Close.\n";
        $buffer->free();
        unset($buffer);
    }
}

function free($buffer, $base)
{
    $buffer->free();
    unset($buffer);
}

