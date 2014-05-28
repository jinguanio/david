<?php

function get_cookie($content) 
{
    $search = preg_match('/Set-Cookie: (MCPHPSID=\w+;) path=\/; HttpOnly/i', $content, $match);
    if (!$search) {
        return '';
    }

    $cookie = isset($match[1]) ? $match[1] : '';
    return $cookie;
}

function get_zone($content)
{
    $search = preg_match('/var gZone = \'(\w{32})\';/i', $content, $match);
    if (!$search) {
        return '';
    }

    $zone = isset($match[1]) ? $match[1] : '';
    return $zone;
}

$ip = '58.68.44.10';
$user = 'libo@eyou.net';
$pwd = 'aaaaa1323';

for ($i = 0, $round = 10; $i < $round; $i++) {
    $request_header = array(
        "CLIENT-IP:{$ip}",
        "X-FORWARDED-FOR:{$ip}",
        "X-Requested-With:xmlhttprequest",
    );

    // 第一次请求，获取登录页
    echo "============= First Response Header =============\n";
    $ch = curl_init("http://172.16.100.213/?q=login");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $content = curl_exec($ch);
    $head = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    echo substr($content, 0, $head);
    //echo $content;

    // 第二次请求，登录验证
    echo "\n============= Second Response =============\n";
    $zone = get_zone($content);
    $cookie = get_cookie($content);

    $post = http_build_query([
        'account' => $user,
        'pass' => $pwd,
        'zone' => $zone,
    ]);
    echo 'Post: ', $post, "\n";
    echo 'Cookie: ', $cookie, "\n\n";

    $ch = curl_init("http://172.16.100.213/?q=login.do&act=login");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $content = curl_exec($ch);
    $head = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    // 切换 ip 地址
    $ip2long = ip2long($ip);
    ++$ip2long;
    $ip = long2ip($ip2long);

    // 第三次请求，转到登录后的首页
    $header = substr($content, 0, $head);
    echo $header;

    $resp = substr($content, $head);
    print_r(json_decode($resp, true));

    echo "\n============= Third Response =============\n";
    $cookie = get_cookie($header);
    echo 'Cookie: ', $cookie, "\n\n";

    $ch = curl_init("http://172.16.100.213/?q=overview&zone={$zone}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $content = curl_exec($ch);
    $head = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    ++$ip2long;
    $header = substr($content, 0, $head);
    echo $header;

    $resp = substr($content, $head);
    //echo $resp, "\n";
    $zone = get_zone($resp);
    echo 'zone=', $zone, "\n";

}

