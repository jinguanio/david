<?php
abstract class base
{
    abstract public function __construct();

    protected function test()
    {
        echo 'base::test', PHP_EOL;
    }
}

class child extends base
{
    /*
    public function __construct()
    {
        //parent::__construct();
        echo $this->test();
        echo 'child::__construct', PHP_EOL;
    }
     */

    public function test2()
    {
        $this->test();
    }
}

$c = new child;
$c->test2();

