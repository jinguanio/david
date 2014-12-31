<?php
$cash = 192620;
$rate = 1 + 2.8/100;
$rate2 = 1 + 4.25/100;
$age_start = 35;
$age_end = 80;

echo <<<H
测算养老保险每年交 $cash 元，缴付 5 年后，银行收益和保险公司收益对比


H;

echo <<<T
本金：$cash
5 年内银行利率：{$rate}（银行 5 年期零存整取）
35 年内银行利率：{$rate2}（银行 5 年期定存）

测算起始年龄：{$age_start}
测算截止年龄：{$age_end}


T;

$a = $cash * $rate;
echo "1 year: " . $a . "\n";
$b = round(($a+$cash) * $rate, 2);
echo "2 year: " . $b . "\n";
$c = round(($b+$cash) * $rate, 2);
echo "3 year: " . $c . "\n";
$d = round(($c+$cash) * $rate, 2);
echo "4 year: " . $d . "\n";
$e = round(($d+$cash) * $rate, 2);
echo "5 year: " . $e . "\n\n";

$total = round($e * pow($rate2, ($age_end - $age_start)), 2);
echo 'Bank: ' . $total . "\n";

$bao_xian = 4938728;
echo "Insurance: $bao_xian" . "\n";

$diff = $total - $bao_xian;
echo 'Diff: ' . abs($diff);
echo "\n\n";

echo "Result: ";
echo ($diff > 0) ? "Bank Win" : "Insurance Company Win";
echo "\n";
