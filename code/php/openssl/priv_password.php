<?php
$pass = '';
$pkey = openssl_pkey_new();
openssl_pkey_export($pkey, $out, $pass);
var_dump($out);

$ret = openssl_pkey_get_private($out, $pass);
var_dump($ret);

openssl_pkey_export($ret, $out1, $pass);
var_dump($out1);

