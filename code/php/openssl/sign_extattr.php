<?php
// 利用第三方证书签名
// 产生 csr 证书，导入 csr 证书，生成 x509 证书
$dn = array(
    "countryName" => "CN",
    "stateOrProvinceName" => "Beijing",
    "localityName" => "Beijing",
    "organizationName" => "Eyou",
    "organizationalUnitName" => "Develop team",
    "commonName" => "Li Bo",
    "emailAddress" => "libo@eyou.net"
);

$config = array(
    'config' => '/etc/pki/tls/openssl.cnf',
    'encrypt_key' => 1,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    "digest_alg" => "sha1",
    'x509_extensions' => 'v3_ca',
    'private_key_bits' => 1024,
    "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC,
);

$serial = [ 123456 ];
$expire = 365;
$csr_extattr = [
    'version' => 1,
    ];
$csr_extattr = [];

$ca_priv = openssl_pkey_new($config); // OpenSSL key
//$ca_csr = openssl_csr_new($dn, $ca_priv, $config, $csr_extattr); // OpenSSL X.509 CSR
$ca_csr = openssl_csr_new($dn, $ca_priv, $config); // OpenSSL X.509 CSR
$ca_cert = openssl_csr_sign($ca_csr, null, $ca_priv, $expire, $serial); // OpenSSL X.509
openssl_x509_export($ca_cert, $ca_certout) and var_dump('CA cert', $ca_certout);
print_r(openssl_x509_parse($ca_certout));

