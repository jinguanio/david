<?php
// 通过私钥建立数据签名(signature)
//data you want to sign
$data = 'my data';

$config = array(
    'config' => '/etc/pki/tls/openssl.cnf',
    'encrypt_key' => 1,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    "digest_alg" => "sha1",
    'x509_extensions' => 'v3_ca',
    'private_key_bits' => 1024,
    "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC,
);
//$config = null;

$password = 'libo';
$password = null;
$password = "\0"; // '' !== null

$new_key_pair = openssl_pkey_new($config);
openssl_pkey_export($new_key_pair, $private_key_pem, $password, $config);

$details = openssl_pkey_get_details($new_key_pair);
$public_key_pem = $details['key'];

if (null !== $password) {
    $private_key = openssl_pkey_get_private($private_key_pem, $password);
    openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
} else {
    openssl_sign($data, $signature, $private_key_pem, OPENSSL_ALGO_SHA256);
}

$base = __DIR__ . '/keys/sign-';
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

$r = openssl_verify($data, $signature, $public_key_pem, "sha256WithRSAEncryption");
var_dump($r);

