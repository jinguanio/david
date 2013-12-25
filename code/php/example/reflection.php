<?php
error_reporting(E_ALL);

class T
{
    private $__a = '__a';
    protected $__b = '__b';
    public $__c = '__c';

    public function a($arg)
    {
        echo "public method a, arg: {$arg}";
    }

    protected function b()
    {
        echo "protected method b";
    }

    private function c()
    {
        echo "private method c";
    }
}

$class = new T;
$ref = new ReflectionClass('T');
$method = $ref->getMethod('a');
$method->invoke($class, 'libo');

