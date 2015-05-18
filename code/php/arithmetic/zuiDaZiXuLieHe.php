<?php
# 最大子序列和
$arr = [4, -3, 5, -2, -1, 2, 6, -2];
$len = count($arr);
$thisSum = $maxSum = 0;
print_r($arr);
echo str_repeat('=', 30), "\n";
for ($j = 0; $j < $len; $j++) {
    $thisSum += $arr[$j];
    echo 'j: ', $j, "\n";
    echo 'thisSum: ', $thisSum, "\n";
    echo 'maxSum: ', $maxSum, "\n";
    echo '---------------------', "\n";

    if ($thisSum > $maxSum) {
        $maxSum = $thisSum;
    } elseif ($thisSum < 0) {
        $thisSum = 0;
    }
}
echo 'result: ', $maxSum, "\n";

