<?php
error_reporting(E_ALL);

$text = 'em_smtpd.rrd';
$trimmed = preg_replace('/.rrd$/', '', $text);
var_dump($trimmed);
