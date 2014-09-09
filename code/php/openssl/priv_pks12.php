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

openssl_csr_export($csr, $csrout) and _var($csrout);
openssl_x509_export($sscert, $cer_x509) and _var($cer_x509);
openssl_pkey_export($privkey, $pkeyout, "mypassword", $config) and _var($pkeyout);
openssl_pkcs12_export($cer_x509, $pkcs12 , $privkey, 'mypassword', $config) && _var(base64_encode($pkcs12));
openssl_pkcs12_read($pkcs12, $cert, 'mypassword') && _var($cert);

//_var(getenv('OPENSSL_CONF'));

// Show any errors that occurred here
//while (($e = openssl_error_string()) !== false) {
//    echo $e . "\n";
//}
//exit;


$cleartext = '1234 5678 9012 3456';
echo "\nClear txt: \n$cleartext\n";

$pub_key = $cert['cert'];
$priv_key = $cert['pkey'];

openssl_public_encrypt($cleartext, $crypttext, $pub_key);
echo "\nCrypt text:\n" . base64_encode($crypttext) . "\n";

openssl_private_decrypt($crypttext, $decrypted, $priv_key);
echo "\nDecrypted text:\n$decrypted\n\n";

//print_r(openssl_x509_parse($certout));

