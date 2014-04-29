<?php
class Counter
{
      private $x;

      public function __construct()
      {
           $this->x = 0;
      }

      private function increment()
      {
           $this->x++;
      }

      private function currentValue()
      {
           echo $this->x . "\n";
      }
}

$object = new Counter();
$reflection = new ReflectionClass('Counter');
$closure = $reflection->getMethod('currentValue')->getClosure($object);
$closure();
$closure = $reflection->getMethod('increment')->getClosure($object);
$closure();
$closure = $reflection->getMethod('currentValue')->getClosure($object);
$closure();

