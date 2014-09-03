<?php
// 通过私钥建立数据签名(signature)
//data you want to sign
$data = 'my data';

//create new private and public key
$new_key_pair = openssl_pkey_new(array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));
openssl_pkey_export($new_key_pair, $private_key_pem);

$details = openssl_pkey_get_details($new_key_pair);
$public_key_pem = $details['key'];

//create signature
openssl_sign($data, $signature, $private_key_pem, OPENSSL_ALGO_SHA256);

//save for later
$base = 'keys/openssl_sign-';
file_put_contents($base . 'private_key.pem', $private_key_pem);
file_put_contents($base . 'public_key.pem', $public_key_pem);
file_put_contents($base . 'signature.dat', $signature);
//var_dump('priv_key: '. base64_encode($private_key_pem), 'pub_key: '. base64_encode($public_key_pem), 'sign: '. base64_encode($signature));

// 检查私钥经过 json_encode 后是否会有异常
// 换行会被转化为 "\n"
//$k = [
//    'priv' => $private_key_pem,
//    ];
//var_dump(json_encode($k));

//verify signature
$r = openssl_verify($data, $signature, $public_key_pem, "sha256WithRSAEncryption");
var_dump($r);

