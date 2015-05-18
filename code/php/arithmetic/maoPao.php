<?php
$arr = [1,43,54,62,21,66,32,78,36,76,39];

function get_pao($arr)
{
    $len = count($arr);
    for ($i = 1; $i < $len; $i++) {
        for ($j = 0; $j < $len - $i; $j++) {
            if ($arr[$j] > $arr[$j+1]) {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j+1];
                $arr[$j+1] = $tmp;
            }
        }
    }

    return $arr;
}

print_r(get_pao($arr));
