<?php
error_reporting(E_ALL);

class Test implements JsonSerializable
{
    private $name = 'libo';
    private $age = 35;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    } 
}

$t = new Test;
print_r(json_encode($t));

