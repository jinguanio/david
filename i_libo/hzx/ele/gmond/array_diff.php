<?php
$context = stream_context_create();

stream_set_timeout($context, 2);
$fp = fopen('/tmp/a.txt', 'r', false, $context);

