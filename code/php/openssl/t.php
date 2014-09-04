<?php
$privateKey = openssl_pkey_new();
var_dump(getenv('OPENSSL_CONF'));
var_dump(getenv('RANDFILE'));
var_dump(getenv('HOME'));
while($message = openssl_error_string()){
    echo $message.PHP_EOL;
}

