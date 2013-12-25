<?php
error_reporting(E_ALL);
var_dump($arr['libo']);
var_dump(empty($arr['libo']));
var_dump(isset($arr['libo']));
exit;

$transport = array('foot' => 1, 'bike' => 2, 'car' => 3, 'plane' => 4);
$mode = end($transport);     // $mode = 'plane';

var_dump($mode);
var_dump(key($transport));

