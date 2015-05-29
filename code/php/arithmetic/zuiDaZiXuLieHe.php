<?php
#
# 最大子序列和
#

// 时间复杂度 O(N^2)
function maxSub($arr)
{
    $n = count($arr);
    $maxSum = $thisSum = 0;
    $s = $e = 0;

    for ($i = 0; $i < $n; $i++) {
        $thisSum = 0;
        #echo "--------", "\n";
        for ($j = $i; $j < $n; $j++) {
            #var_dump($arr[$j]);
            $thisSum += $arr[$j];
            if ($thisSum > $maxSum) {
                $maxSum = $thisSum;
                $s = $i;
                $e = $j;
            }
        }
    }

    echo __FUNCTION__, "({$s}~{$e}): ", $maxSum, "\n";
}

// 算法不正确
// 时间复杂度 O(N)
// http://blog.csdn.net/joylnwang/article/details/6859677
// 太复杂，看不懂
function maxSub2($arr)
{
    $n = count($arr);
    $thisSum = $maxSum = 0;
    $s = $e = 0;

    for ($j = 0; $j < $n; $j++) {
        $thisSum += $arr[$j];
        #echo 'j: ', $j, "\n";
        #echo 'thisSum: ', $thisSum, "\n";
        #echo 'maxSum: ', $maxSum, "\n";
        #echo '---------------------', "\n";

        if ($thisSum > $maxSum) {
            $maxSum = $thisSum;
            $e = $j;
        } elseif ($thisSum < 0) {
            $thisSum = 0;
            $e = $s = $j + 1;
        }
    }

    echo __FUNCTION__, "({$s}~{$e}): ", $maxSum, "\n";
}

$arr = [ 4, -3, 5, -2, -1, 2, 6, -2 ];
$arr = [ 4, 3, 5 ];

echo json_encode($arr), "\n";
maxSub($arr);
maxSub2($arr);

