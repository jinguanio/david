<?php
class T
{
    public function __call($name, $arg)
    {
        var_dump($name, $arg);
    }
}

$t = new T;
$t->test();
$t->test('aaa');
$t->test([ 1,2,3 ]);
$t->test([ 1,2,3 ], 22);

