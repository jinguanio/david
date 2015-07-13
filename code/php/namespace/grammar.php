<?php
namespace libo;
class T
{
    public function e()
    {
        echo 'namespace: ' . __NAMESPACE__ . ', method: ' . __METHOD__ . "\n";
    }
}

namespace bnn;
class T
{
    public function e()
    {
        echo 'namespace: ' . __NAMESPACE__ . ', method: ' . __METHOD__ . "\n";
    }
}

use \libo as L;
echo 'Current Namespace: ' . __NAMESPACE__ . "\n";

$t1 = new L\T;
$t2 = new \bnn\T;

echo "\$t1 object\n";
$t1->e();

echo "\$t2 object\n";
$t2->e();
