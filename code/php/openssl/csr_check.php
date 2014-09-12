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

$ca_pubout = openssl_pkey_get_details($ca_priv)['key']; //var_dump($ca_pubout);
openssl_pkey_export($ca_priv, $ca_privout, $password, $config); // && var_dump('CA Private', $ca_privout);
openssl_x509_export($ca_cert, $ca_certout); // and var_dump('CA cert', $ca_certout);

$path = __DIR__ . '/ca';
file_put_contents($path . '/ca_cert.cer', $ca_certout);
file_put_contents($path . '/ca_priv.pem', $ca_privout);

// SELF
$priv = openssl_pkey_new($config);
$csr = openssl_csr_new($dn, $priv, $config);
openssl_csr_export($csr, $csrout);

// ********** 签署证书 **********
// 自签证书
$cert_self = openssl_csr_sign($csrout, null, $priv, 365);
openssl_x509_export($cert_self, $certout_self);

// CA 签证书
$cert_ca = openssl_csr_sign($csr, $ca_certout, [ $ca_privout, $password ], 365);
openssl_x509_export($cert_ca, $certout_ca);

$cert_other_file = __DIR__ . '/keys/cert-x509.crt';
$certout_other = file_get_contents($cert_other_file);

// 验证公私钥是否成对
function check_pair($cert, $priv)
{
    $msg = openssl_x509_check_private_key($cert, $priv) ? '+Ok, Match' : '-Err, Not Match';
    echo $msg . "\n\n";
}

echo "check a pair of keys: another cert and private: \n";
check_pair($certout_other, $priv);

echo "check a pair of keys: cert signed by CA and private: \n";
check_pair($cert_ca, $priv);

echo "check a pair of keys: cert signed by self and private: \n";
check_pair($cert_self, $priv);

// 验证证书是否有效
function check_cert($cert)
{

    // 只能是文件路径，可以是多个 x509 证书
    // 不能是 pem 格式字符串和 OpenSSL X.509 资源
    $cainfo = __DIR__ . '/ca/ca_cert.cer';

    $purpose = [
        //X509_PURPOSE_SSL_CLIENT,
        //X509_PURPOSE_SSL_SERVER,
        //X509_PURPOSE_NS_SSL_SERVER,
        //X509_PURPOSE_SMIME_SIGN,
        //X509_PURPOSE_SMIME_ENCRYPT,
        //X509_PURPOSE_CRL_SIGN,
        X509_PURPOSE_ANY,
        ];

    foreach ($purpose as $p) {
        var_dump(openssl_x509_checkpurpose($cert, $p, [ $cainfo ] ));
    }
}

echo "check certificate valid: signed ca\n";
check_cert($certout_ca);

echo "\ncheck certificate valid: signed self\n";
check_cert($certout_self);

echo "\ncheck certificate valid: another certificate\n";
check_cert($certout_other);
echo "\n";

openssl_pkey_export($priv, $priv_key, null, $config);
$cleartext = '1234 5678 9012 3456';
echo "Clear txt: \n$cleartext\n";

openssl_public_encrypt($cleartext, $crypttext, $certout_self); // right
echo "\nCrypt signed self text:\n" . base64_encode($crypttext) . "\n";

openssl_public_encrypt($cleartext, $crypttext, $certout_ca); // right
echo "\nCrypt signed CA text:\n" . base64_encode($crypttext) . "\n";

openssl_private_decrypt($crypttext, $decrypted, $priv_key);
echo "\nDecrypted text:\n$decrypted\n\n";

