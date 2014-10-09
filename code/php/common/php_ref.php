<?php
function f1($a)
{
    $a = 'changed by f1';
}

function f2(&$a)
{
    $a = 'changed by f2';
}

function f3($a)
{
    $a = 'changed by f3';
}

$a = '$a';
echo $a, PHP_EOL;

f1($a);
echo $a . PHP_EOL;
echo '==============' . PHP_EOL;

$a = '$a';
echo $a, PHP_EOL;

f2($a);
echo $a . PHP_EOL;
echo '==============' . PHP_EOL;

$a = '$a';
echo $a, PHP_EOL;

f3(&$a);
echo $a . PHP_EOL;
echo '==============' . PHP_EOL;

