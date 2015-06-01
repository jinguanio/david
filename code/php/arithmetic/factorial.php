<?php
#
# 斐波那契数列求和操作
#

function Factorial($n)
{
    if ($n <= 1) {
        return 1;
    } else {
        return $n + Factorial($n-1);
    }
}

echo 'Factorial Sum: ', Factorial(4);

