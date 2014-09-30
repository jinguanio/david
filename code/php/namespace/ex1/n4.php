<?php
namespace ns3;

require_once __DIR__ . '/n2.php';
require_once __DIR__ . '/n1.php';
use ns2, ns1;

require_once __DIR__ . '/g1.php';

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

echo "=========== " . __NAMESPACE__ . " ============\n";
var_dump(new c, f(), V);

echo "=========== ns 2 ============\n";
var_dump(new ns2\c, ns2\f(), ns2\V);

echo "=========== ns 2 ============\n";
var_dump(new \ns2\c, \ns2\f(), \ns2\V);

echo "=========== global 1 ============\n";
var_dump(new \c, \f(), \V);

