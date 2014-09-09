<?php
$res = openssl_pkey_get_private('file:///home/libo/git/david/code/php/openssl/private_key.pem');
$ret = openssl_pkey_get_details($res);
$pub_key = $ret['key'];

$pub_pem = file_get_contents('public_key.pem');
echo (0 === strcmp($pub_key, $pub_pem)) ? "Equals" : "Not Equals";
echo "\n";

