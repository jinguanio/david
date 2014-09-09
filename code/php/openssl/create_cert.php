<?php
function _var($mixed, $is_dump = false)
{
    if ($is_dump) {
        var_dump($mixed);
    }
}

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

$privkey = openssl_pkey_new($config);
$csr = openssl_csr_new($dn, $privkey);
$sscert = openssl_csr_sign($csr, null, $privkey, 365);


$path = __DIR__ . '/keys';
$path_pub = "$path/cert-x509.crt";
$path_priv = "$path/cert-pkcs12.pfx";

openssl_csr_export($csr, $csrout) and _var($csrout);
openssl_x509_export_to_file($sscert, $path_pub);

// export to pfx style
// PKCS #12（公钥加密标准 #12）是业界格式，适用于证书及相关私钥的传输、备份和还原。
$pub_key = file_get_contents($path_pub);
openssl_pkcs12_export_to_file($pub_key, $path_priv, $privkey, 'mypassword', $config);

while (($e = openssl_error_string()) !== false) {
    echo $e . "\n";
}

echo "ok, create certificate/private-key";

