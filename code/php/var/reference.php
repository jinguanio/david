<?php
$arr = [ 'a' => [ 'b' => 1, 'c' => 2 ] ];
print_r($arr);

$a = $arr['a'];
$a['b'] = 3;
print_r($arr);

$a =& $arr['a'];
$a['b'] = 3;
print_r($arr);

