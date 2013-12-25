<?php
$str = 'sasas';
$controller = strtok($str, '/');
echo $controller, PHP_EOL;
$action = strtok('/');
echo $action, PHP_EOL;
