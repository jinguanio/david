<?php
function _var($mixed, $is_dump = false)
{
    if ($is_dump) {
        var_dump($mixed);
    }
}

$path = __DIR__ . '/keys';
$path_pub = "$path/cert-x509.crt";
$path_priv = "$path/cert-pkcs12.pfx";

$pfx = file_get_contents($path_priv);
openssl_pkcs12_read($pfx, $cert, 'mypassword') && _var($cert);

$x509 = file_get_contents($path_pub);
//print_r(openssl_x509_parse($x509));
$pub_key = openssl_pkey_get_public($x509);

//_var(getenv('OPENSSL_CONF'));

// Show any errors that occurred here
$cleartext = '1234 5678 9012 3456';
echo "Clear txt: \n$cleartext\n";

$priv_key = $cert['pkey'];

openssl_public_encrypt($cleartext, $crypttext, $pub_key);
echo "\nCrypt text:\n" . base64_encode($crypttext) . "\n";

openssl_private_decrypt($crypttext, $decrypted, $priv_key);
echo "\nDecrypted text:\n$decrypted\n\n";

//print_r(openssl_x509_parse($certout));

while (($e = openssl_error_string()) !== false) {
    echo $e . "\n";
}

