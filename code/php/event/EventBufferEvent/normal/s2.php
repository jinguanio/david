<?php
error_reporting(E_ALL);

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
    $buffer->enable(Event::READ);

    $data = 'server.hello';
    $length = strlen($data);
    $buffer->write($data);
    echo "write output buffer, data: server.hello({$length})\n";
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
    echo "read running...\n";

    $data = '';
    while ($read = $buffer->read(1024)) {
        $data .= $read;
    }
    echo 'client data: ', $data, PHP_EOL;
}

function callback_write($buffer, $base)
{
    echo "write running...\n";


    // 没有释放 EventBufferEvent 会产生
    // Event::EOF 情况
    free($buffer, $base);
    echo "close server.\n";
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

