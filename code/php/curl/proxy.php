<?php
$url = "http://172.16.100.213:3333/target_ip.php";
$user_agent = "Mozilla/1.0";
$proxy = "http://172.16.100.114:8000";
$header = array(
    'CLIENT-IP: 58.68.44.63',
    'X-FORWARDED-FOR: 58.68.44.64',
);

$ch = curl_init();
curl_setopt ($ch, CURLOPT_PROXY, $proxy);
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
curl_setopt ($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt ($ch, CURLOPT_HEADER, 1);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
$result = curl_exec ($ch);
curl_close($ch);

echo $result;
