<?php
#
# 最大公约数
#
$m = 1989;
$n = 1590;

while ($n > 0) {
    $rem = $m % $n;
    $m = $n;
    $n = $rem;
    #echo $n, "\n";
}

echo 'result: ', $m, "\n";

