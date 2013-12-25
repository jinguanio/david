<?php
error_reporting(E_ALL);

function _g($k)
{
    return '你好';
}

$arr = [
    _g('title_a') => 'a',
    'title_b' => 'b',
    ('title_c') => 'a',
    2 => 'c',
    ];

print_r($arr);
print_r(array_keys($arr));
print_r(array_unique($arr));

