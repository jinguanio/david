<?php
class T
{
    public $__data = [1,2,3,4];
    public $__libo = 'name';

    public function __sleep()
    {
        return ['__data'];
    }

    public function _echo()
    {
        print_r($this->__data);
    }
}

$t = new T;
var_dump($t);
$ret = serialize($t);
var_dump($ret);

$c = unserialize($ret);
var_dump($c);

var_dump($c === $t);

