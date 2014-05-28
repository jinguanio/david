<?php
$ip = '58.34.61.22';
$request_header = array(
    "CLIENT-IP:{$ip}",
    "X-FORWARDED-FOR:{$ip}",
    "REMOTE_ADDR:{$ip}",
);

// 第一次请求，获取登录页
$ch = curl_init("http://127.1:3333/target_ip.php");
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
$content = curl_exec($ch);
$head = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);
echo $content;


