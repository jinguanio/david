<?php
#
# 快速排序算法
#
function kuaisu($arr)
{
    $len = count($arr);
    if ($len <= 1) {
        return $arr;
    }

    $base = $arr[0];
    $left = $right = [];
    for ($i = 1; $i < $len; $i++) {
        if ($arr[$i] > $base) {
            $right[] = $arr[$i];
        } else {
            $left[] = $arr[$i];
        }
    }

    $left = kuaisu($left);
    $right = kuaisu($right);
    return array_merge($left, [$base], $right);
}

$arr = [1,43,54,62,21,66,32,78,36,76,39];
print_r(kuaisu($arr));

