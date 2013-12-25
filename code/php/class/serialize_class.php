<?php
/*
class tt
{
    private $a = 4;
    protected $b = 5;
    public $c = 6;

    public function d()
    {
    }

    private function c()
    {
    }

    protected function e()
    {
    }
    
}

class test
{
    private $a = 1;
    protected $b = 2;
    public $c = 3;

    public $o = null;

    public function d()
    {
    }

    private function c()
    {
    }

    protected function e()
    {
    }

    
}

$t2 = new tt;
$t = new test;
$t->o = $t2;
$s = serialize($t);

file_put_contents('/tmp/xml', $s);
 */
//$s = 'O:4:"test":3:{s:7:"testa";i:1;s:4:"*b";i:2;s:1:"c";i:5;}';

$s = file_get_contents('/tmp/xml');
$n = unserialize($s);
echo $n->o->c;
