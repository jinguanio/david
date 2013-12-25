<?php
// Read callback
function readcb($bev, $base) {
    echo "readcb\r\n";
    $input = $bev->getInput();

    do {
        $buf = $input->read(1024);
        echo $buf;
    } while(null !== $buf);
}

// Event callback
function eventcb($bev, $stat, $base) {
    var_dump('status: '. $stat);
    if ($stat & EventBufferEvent::CONNECTED) {
        echo "Connected.\n";
    } elseif ($stat & (EventBufferEvent::ERROR | EventBufferEvent::EOF)) {
        if ($stat & EventBufferEvent::ERROR) {
            echo "DNS error: ", $bev->getDnsErrorString(), PHP_EOL;
        }

        echo "Closing\n";
        $base->exit();
        exit("Done\n");
    } elseif ($stat & EventBufferEvent::TIMEOUT) {
        echo "Timeout\n";
    }
}

function writecb($bev, $base)
{
    echo "writecb\r\n";
    $bev->output->add(microtime(true) . "\r\n");
}

$base = new EventBase();

$bev = new EventBufferEvent($base, null,
    EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::TIMEOUT,
    "readcb", null, "eventcb", $base);
    //"readcb", "writecb", "eventcb", $base);
$bev->enable(Event::READ | Event::WRITE);
$bev->getOutput()->add(microtime(true) . "\r\n");
$bev->setTimeouts(2, 2);
$bev->connect('127.0.0.1:8000');

$base->dispatch();

