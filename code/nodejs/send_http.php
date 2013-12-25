<?php
error_reporting(E_ALL);

while (1) {
    exec('iostat', $out);

    $data = array('data' => json_encode($out));
    $data = http_build_query($data);

    try {
        //$req = new HttpRequest('http://172.16.100.114:3333/receive_http.php', HTTP_METH_POST);
        $req = new HttpRequest('http://172.16.100.114:3000', HTTP_METH_POST);
        $req->setBody($data);
        $req->send();
    } catch (Exception $e) {
        echo 'ERR: ', $e->getMessage(), "\n";
        exit(1);
    }

    if (200 === $req->getResponseCode()) {
        $body = $req->getResponseBody();
        print_r(json_decode($body, true));
    } else {
        $body = 'error';
    }

    unset($out);
    echo date('r') . "\n";
    sleep(1);
}

