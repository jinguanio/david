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

// CA
$password = '123456';
$ca_priv = openssl_pkey_new($config); // OpenSSL key
$ca_csr = openssl_csr_new($dn, $ca_priv, $config); // OpenSSL X.509 CSR
$ca_cert = openssl_csr_sign($ca_csr, null, $ca_priv, 365); // OpenSSL X.509

$ca_pubout = openssl_pkey_get_details($ca_priv)['key']; var_dump($ca_pubout);
openssl_pkey_export($ca_priv, $ca_privout, null, $config) && var_dump('CA Private', $ca_privout);
openssl_x509_export($ca_cert, $ca_certout) and var_dump('CA cert', $ca_certout);
openssl_pkcs12_export($ca_certout, $ca_pfx, $ca_privout, $password) && var_dump('CA pfx', base64_encode($ca_pfx));
echo "\n\n";

// SELF
$priv = openssl_pkey_new($config);
$csr = openssl_csr_new($dn, $priv, $config);
openssl_csr_export($csr, $csrout);
//var_dump($csrout);
//var_dump($csr);
//exit;

// ********** 签署证书 **********
//$cert = openssl_csr_sign($csr, null, $priv, 365);
//$cert = openssl_csr_sign($csrout, null, $priv, 365);
// CA 签证书
//$cert = openssl_csr_sign($csr, $ca_certout, $ca_pfx, 365); // wrong
//$cert = openssl_csr_sign($csr, $ca_pubout, $ca_privout, 365); // wrong
$cert = openssl_csr_sign($csr, $ca_certout, $ca_privout, 365); // right

// ********* 导出证书 ***********
openssl_csr_export($csr, $csrout) and var_dump('CSR', $csrout);
openssl_x509_export($cert, $certout) and var_dump('Certificate', $certout);
openssl_pkey_export($priv, $pkeyout, $password, $config) and var_dump('Private', $pkeyout);
$pkey = openssl_pkey_get_private($pkeyout, $password);
// $pkey 参数可以是没有密码导出的密钥
// 或者是 OpenSSL key 资源
openssl_pkcs12_export($certout, $pfx, $pkey, $password);
openssl_pkcs12_read ($pfx, $certs, $password) && var_dump($certs);

$cleartext = '1234 5678 9012 3456';
echo "Clear txt: \n$cleartext\n";

// ************ 公私钥 ***************
$pub_key = $certout; // right
//$pub_key = openssl_pkey_get_public($certout); // right OpenSSL key
//$pub_key = openssl_pkey_get_details($priv)['key']; // right public key
//$pub_key = $csrout; // wrong

$priv_key = openssl_pkey_get_private($pkeyout, $password); // right OpenSSL key
//$priv_key = $pkeyout; // wrong private key
//$priv_key = $pfx; // wrong pcks12 cert
//$priv_key = $priv; // right OpenSSL key
//openssl_pkey_export($priv, $priv_key, null, $config);

openssl_public_encrypt($cleartext, $crypttext, $pub_key); // right
echo "\nCrypt text:\n" . base64_encode($crypttext) . "\n";

openssl_private_decrypt($crypttext, $decrypted, $priv_key);
echo "\nDecrypted text:\n$decrypted\n\n";

//print_r(openssl_x509_parse($certout));

