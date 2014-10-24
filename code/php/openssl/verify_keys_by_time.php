<?php
$dir = basename($argv[0], '.php');
$path = __DIR__ . "/$dir/";

$file_ca_x509 = $path . 'CA_x509.crt'; 
$file_ca_pkey = $path . 'CA_pkey.pem';
$file_x509 = $path . 'x509.crt'; 
$file_pkcs12 = $path . 'pkcs12.pfx';
$file_contents = $path . 'content';

$pass = null;
$plain = 'hello libo';
$root_expire_time = 2;
$expire_time = 3;

$config = [
    'config' => '/etc/pki/tls/openssl.cnf',
    'encrypt_key' => 1,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    "digest_alg" => "sha1",
    'x509_extensions' => 'v3_ca',
    'private_key_bits' => 1024,
    "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC,
];
$dn = [
    "countryName"            => "CN",
    "stateOrProvinceName"    => "Frankfurt",
    "organizationName"       => "smcc.net",
    "organizationalUnitName" => "E-Mail",
    "commonName"             => "Testcert"
];


function create_ca()
{
    global $file_ca_pkey, $file_ca_x509;
    global $pass, $config, $dn, $root_expire_time;

    $ca_key = openssl_pkey_new($config); // OpenSSL key
    $ca_csr = openssl_csr_new($dn, $ca_key, $config); // OpenSSL X.509 CSR
    $ca_cert = openssl_csr_sign($ca_csr, null, $ca_key, $root_expire_time); // OpenSSL X.509

    $ret = openssl_x509_export_to_file($ca_cert, $file_ca_x509);
    if (!$ret) {
        while ($msg = openssl_error_string())
            echo $msg . "<br />\n";
        echo "-Err, create CA x509 fail!(" . __LINE__ . ")\n";
        exit(1);
    }

    $ret = openssl_pkey_export_to_file($ca_key, $file_ca_pkey, $pass, $config);
    if (!$ret) {
        while ($msg = openssl_error_string())
            echo $msg . "<br />\n";
        echo "-Err, create CA pkey fail!(" . __LINE__ . ")\n";
        exit(1);
    }

    echo "+Ok, create CA succ!\n";
}

function create_cert()
{
    global $file_pkcs12, $file_x509, $file_ca_x509, $file_ca_pkey;
    global $pass, $config, $dn, $expire_time;

    $ca_x509 = file_get_contents($file_ca_x509);
    $ca_pkey = file_get_contents($file_ca_pkey);

    $req_key = openssl_pkey_new($config);
    $req_csr  = openssl_csr_new ($dn, $req_key);

    // CA sign
    $req_cert = openssl_csr_sign($req_csr, $ca_x509, [ $ca_pkey, $pass ], $expire_time);

    // SELF sign
    // 自签证书不能验证有效期
    //$req_cert = openssl_csr_sign($req_csr, null, $req_key, $expire_time);

    $ret = openssl_x509_export_to_file($req_cert, $file_x509);
    if (!$ret) {
        while ($msg = openssl_error_string())
            echo $msg . "<br />\n";
        echo "-Err, create x509 fail!(" . __LINE__ . ")\n";
        exit(1);
    }

    $ret = openssl_pkcs12_export_to_file($req_cert, $file_pkcs12, $req_key, $pass);
    if (!$ret) {
        while ($msg = openssl_error_string())
            echo $msg . "<br />\n";
        echo "-Err, create pkcs12 fail!(" . __LINE__ . ")\n";
        exit(1);
    }

    echo "+Ok, create keys succ!\n";
}

function check_cert()
{
    global $file_x509, $file_pkcs12, $file_ca_x509;
    global $pass;

    $pkcs12 = file_get_contents($file_pkcs12);
    $x509 = file_get_contents($file_x509);

    // 可以检查证书有效期
    // 有效期和系统时间有关(date)
    if ($ret = openssl_x509_checkpurpose($x509, X509_PURPOSE_ANY, [ $file_ca_x509 ])) {
        echo "+Ok, check public key succ!\n";
    } else {
        if (false === $ret) {
            echo "-Err, public key cannot be used!(" . __LINE__ . ")\n";
            exit(1);
        } elseif (-1 === $ret) {
            echo "-Err, check public key on error!(" . __LINE__ . ")\n";
            exit(1);
        }
    }

    openssl_pkcs12_read($pkcs12, $certs, $pass);
    if (openssl_x509_check_private_key($x509, $certs['pkey'])) {
        echo "+Ok, match public key with private key succ!\n";
    } else {
        echo "-Err, match public key with private key fail!(" . __LINE__ . ")\n";
        exit(1);
    }
}

function encrypt()
{
    global $file_x509, $file_contents;
    global $plain;

    $x509 = file_get_contents($file_x509);

    $ret = openssl_public_encrypt($plain , $crypted , $x509);
    if (!$ret) {
        while ($msg = openssl_error_string())
            echo $msg . "<br />\n";
        echo "-Err, encrypt fail!(" . __LINE__ . ")\n";
        exit(1);
    }
    file_put_contents($file_contents, $crypted);
    echo "+Ok, encrypt succ!\n";
}

function decrypt()
{
    global $file_pkcs12, $file_contents;
    global $pass, $plain;

    $pkcs12 = file_get_contents($file_pkcs12);
    $contents = file_get_contents($file_contents);

    openssl_pkcs12_read($pkcs12, $cert, $pass);
    while ($msg = openssl_error_string())
        echo $msg . "<br />\n";

    openssl_private_decrypt($contents, $decrypted, $cert['pkey']);
    if ($plain === $decrypted) {
        echo "+Ok, decrypt succ!\n";
    } else {
        echo "-Err, decrypt fail!(" . __LINE__ . ")\n";
    }
}

if (!is_dir($path)) {
    mkdir($path, 0775);
} else {
    exec("rm -fr {$path}/*", $out, $ret);
}

create_ca();
create_cert();
check_cert();
encrypt();
decrypt();

/*
 * 测试：
 * （1）CA 证书不过期，用户证书过期
 * （2）CA 证书过期，用户证书不过期
 *
 * 结论：
 * （1）CA 证书过期，则用户证书验证失败
 * （2）CA 证书有效，用户证书过期，则验证失败
 */

