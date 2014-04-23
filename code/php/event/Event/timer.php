<?php
function display($data) {
    global $base, $ev;

    echo $data, "\n";

    if ($ev) {
        $ev->delTimer();
    }
    timer();
}

function timer()
{
    global $base, $ev;
    $ev = Event::timer($base, 'display', 'libo');
    $ev->addTimer(3);
}

$ev = null;
$base = new EventBase();
timer();
$base->dispatch();

