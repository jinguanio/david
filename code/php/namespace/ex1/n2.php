<?php
namespace ns2;

class c {
    public function e()
    {
        echo __METHOD__;
    }
}

function f() {
    return __FUNCTION__ . '()';
} 

const V = __FILE__;

//var_dump(new c, f(), V);

