<?php
namespace ns1\client\message;

require_once __DIR__ . '/n1.php';
use ns1;

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

echo "================== " . __NAMESPACE__ . " =================\n";
var_dump(new c, f(), V);

echo "================== ns1 =================\n";
var_dump(new ns1\c, ns1\f(), ns1\V);

