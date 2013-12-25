<?php
// Read callback
function readcb($bev, $base) {
    $input = $bev->getInput();

    do {
        $buf = $input->read(1024);
        echo $buf;
    } while(null !== $buf);
}

// Event callback
function eventcb($bev, $events, $base) {
    if ($events & EventBufferEvent::CONNECTED) {
        echo "Connected.\n";
    } elseif ($events & (EventBufferEvent::ERROR | EventBufferEvent::EOF)) {
        if ($events & EventBufferEvent::ERROR) {
            echo "DNS error: ", $bev->getDnsErrorString(), PHP_EOL;
        }

        echo "Closing\n";
        $base->exit();
        exit("Done\n");
    }
}

if ($argc != 3) {
    echo <<<EOS
Trivial HTTP 0.x client
Syntax: php {$argv[0]} [hostname] [resource]
Example: php {$argv[0]} www.google.com /

EOS;
    exit();
}

$base = new EventBase();
$dns_base = new EventDnsBase($base, TRUE); 

$bev = new EventBufferEvent($base, /* use internal socket */ null,
    EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS,
    "readcb", /* writecb */ null, "eventcb", $base);
$bev->enable(Event::READ | Event::WRITE);
$bev->getOutput()->add(
    "GET {$argv[2]} HTTP/1.0\r\n".
    "Host: {$argv[1]}\r\n".
    "Connection: Close\r\n\r\n"
);
$bev->connectHost($dns_base, $argv[1], 80, EventUtil::AF_UNSPEC);

$base->dispatch();

