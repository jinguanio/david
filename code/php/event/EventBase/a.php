<?php
function print_line($fd, $what, $base)
{
    echo fgets($fd);
}

$base = new EventBase();
$event = new Event($base, STDIN, Event::READ | Event::PERSIST, 'print_line', $base);
$event->add();

while (1) {
    $base->exit(6);
    echo '===========debug==========='.PHP_EOL;
    $base->loop();
}

