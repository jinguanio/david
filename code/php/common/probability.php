<?php
//数组乱序排列（完美）
function rand_array($arr)
{
    //获得数组大小
    $arr_size = sizeof($arr);

    //初始化结果数组
    $tmp_arr=array();

    //开始乱序排列
    for($i=0;$i<$arr_size;$i++) {
        //随机配置种子，减少重复率
        mt_srand((double) microtime()*1000000);

        //获得被配置的下标
        $rd=mt_rand(0,$arr_size-1);

        //下标是否已配置
        if(!isset($tmp_arr[$rd])) { //未配置
            $tmp_arr[$rd]=$arr[$i];
        } else { //已配置
            //返回
            $i = $i-1;
        }
    }

    return $tmp_arr;
}

function cal_probability($search)
{
    global $res, $arr_size;

    preg_match_all('/'.$search.'/', $res, $alpha);
    return count($alpha[0]) . '%';
}

$res = '';
$arr = array();
$arr_size = 100;

for ($i = 0; $i < $arr_size; $i++) {
    switch (true) {
    case ($i < 30):
        $arr[] = 'a';
        break;

    case ($i > 31 && $i < 60):
        $arr[] = 'b';
        break;

    default:
        $arr[] = 'c';
    }
}

$arr = rand_array($arr);
for ($i = 0; $i < $arr_size; $i++) {
    $res .= $arr[rand(0, 99)];
}
echo 'result: ', $res, PHP_EOL;
echo '<a> count: ', cal_probability('a'), PHP_EOL; 
echo '<b> count: ', cal_probability('b'), PHP_EOL; 
echo '<c> count: ', cal_probability('c'), PHP_EOL; 

