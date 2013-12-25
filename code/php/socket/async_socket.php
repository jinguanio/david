<?php
error_reporting(E_ALL);

function triggerRequest($url, $post_data = array(), $cookie = array())
{
    //可以通过POST或者GET传递一些参数给要触发的脚本
    $method = "GET";
    //获取URL信息，以便平凑HTTP HEADER
    $url_array = parse_url($url);
    $port = isset($url_array['port'])? $url_array['port'] : 80;

    $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
    if (!$fp) {
        die('create socket fail.');
    }

    $get_path = $url_array['path'];
    if (isset($url_array['query'])) {
        $get_path .= "?". $url_array['query'];
    }

    if(!empty($post_data)) {
        $method = "POST";
    }

    $header = $method . " " . $get_path;
    $header .= " HTTP/1.1\r\n";
    $header .= "Host: ". $url_array['host'] . ":{$port}\r\n "; //HTTP 1.1 Host域不能省略
    // 注意 header 后面要接两组\r\n
    $header .= "Connection: Close\r\n\r\n";
   
    if(!empty($cookie)) {
        $_cookie = strval(NULL);
        foreach($cookie as $k => $v) {
            $_cookie .= "{$k}={$v}";
        }
        $cookie_str =  "Cookie: " . base64_encode($_cookie) ." \r\n";//传递Cookie
        $header .= $cookie_str;
    }

    if(!empty($post_data)) {
        $_post = strval(NULL);
        foreach($post_data as $k => $v) {
            $_post .= $k."=".$v."&";
        }
        $post_str  = "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
        $post_str .= "Content-Length: ". strlen($_post) ." \r\n";//POST数据的长度
        $post_str .= $_post."\r\n\r\n "; //传递POST数据
        $header .= $post_str;
    }

    //var_dump(nl2br($header));
    fwrite($fp, $header);
    fclose($fp);
}

$url = 'http://172.16.100.120:3333/php/async_socket_server.php?name=libo&age=' . mt_rand(0, 40);
triggerRequest($url);

