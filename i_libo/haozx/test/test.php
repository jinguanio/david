<?php
$cookie_file = tempnam('/tmp', 'cookie');
$fp = curl_init('http://127.0.0.1/user/?q=login.do');

curl_setopt($fp, CURLOPT_POST, 1);

$post_data = 'user=user_1%40domain1.com&password=aaaaa123&login_ssl=0';
curl_setopt($fp, CURLOPT_POSTFIELDS, $post_data);
//把返回来的cookie信息保存在$cookie_file文件中
curl_setopt($fp, CURLOPT_COOKIEJAR, $cookie_file);

//设定返回的数据是否自动显示
curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);

//设定是否显示头信息
curl_setopt($fp, CURLOPT_HEADER, false);

//设定是否输出页面内容
curl_setopt($fp, CURLOPT_NOBODY, false);

$arr = curl_exec($fp);

curl_close($fp);

var_dump($arr);
//get data after login
$fp = curl_init('http://127.0.0.1/?q=base');
curl_setopt($fp, CURLOPT_HEADER, false);
curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($fp, CURLOPT_COOKIEFILE, $cookie_file);

$orders = curl_exec($fp);
echo strip_tags($orders);
curl_close($fp);
$end = microtime(true);
echo $end - $start;
