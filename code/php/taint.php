<?php
$q = $_GET['q'];
//$q .= '.php';
//$q = strval($q);
list($a, $b) = explode('.', $q);

include_once "{$a}.{$b}";
print_r($array);

// $name = $_GET["name"];
//   $value = strval($_GET["tainted"]);
// 
//   echo $$name;
