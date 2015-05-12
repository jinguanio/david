<?php
// 先比较最小，逐渐后移指针
function bubble_sort($arr) {
    $n = count($arr);
    for($i=0;$i<$n-1;$i++){
        for($j=$i+1;$j<$n;$j++) {
            if($arr[$j]<$arr[$i]) {
                $temp=$arr[$i];
                $arr[$i]=$arr[$j];
                $arr[$j]=$temp;
            }
        }
    }
    return $arr;
}

$arr = [2,1,4,5,3];
print_r(bubble_sort($arr));

