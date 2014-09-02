<?php
// Create the keypair
$res = openssl_pkey_new();

// Get private key
openssl_pkey_export($res, $privatekey);

// Get public key
$publickey = openssl_pkey_get_details($res);
$publickey = $publickey["key"];

echo "Private Key:\n$privatekey\n\nPublic Key:\n$publickey\n\n";

$cleartext = '1234 5678 9012 3456';

echo "Clear text:\n$cleartext\n\n";

openssl_public_encrypt($cleartext, $crypttext, $publickey);

echo "Crypt text:\n" . base64_encode($crypttext) . "\n";

openssl_private_decrypt($crypttext, $decrypted, $privatekey);

echo "\nDecrypted text:\n$decrypted\n\n";

