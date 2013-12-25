<?php
function display($data) {
    echo $data;
}

$base = new EventBase();
$ev = Event::timer($base, 'display', 'libo');
$ev->add(3);
$base->dispatch();

