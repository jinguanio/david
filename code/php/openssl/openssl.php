<?php
function t1() 
{
    $config = array(
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
    );
    //$config = [];

    // Create the keypair
    $res = openssl_pkey_new($config);

    // Get private key
    openssl_pkey_export($res, $privatekey);

    // Get public key
    $publickey = openssl_pkey_get_details($res);
    $publickey = $publickey["key"];
    //exit;

    echo "Private Key:\n$privatekey\n\nPublic Key:\n$publickey\n\n";

    $cleartext = '1234 5678 9012 3456';

    echo "Clear text:\n$cleartext\n\n";

    openssl_public_encrypt($cleartext, $crypttext, $publickey);

    echo "Crypt text:\n" . base64_encode($crypttext) . "\n";

    openssl_private_decrypt($crypttext, $decrypted, $privatekey);

    echo "\nDecrypted text:\n$decrypted\n\n";
}

function t2() 
{
    $config = array(
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "encrypt_key" => 1,
        "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC,
    );

    // Create the keypair
    $res = openssl_pkey_new($config);

    // Get private key
    $file = __DIR__ . '/keys/openssl-priv.pem';
    openssl_pkey_export_to_file($res, $file, 'libo', $config);

    // Get public key
    $publickey = openssl_pkey_get_details($res);
    $publickey = $publickey["key"];
    //exit;

    echo "Private Key:\n" . file_get_contents($file) . "\n\nPublic Key:\n$publickey\n\n";

    $cleartext = '1234 5678 9012 3456';

    echo "Clear text:\n$cleartext\n\n";

    openssl_public_encrypt($cleartext, $crypttext, $publickey);

    echo "Crypt text:\n" . base64_encode($crypttext) . "\n";

    $priv = openssl_pkey_get_private ("file://{$file}", "libo");
    if (!$priv) {
        echo "\nGet private key fail!\n";
        exit(1);
    }
    openssl_private_decrypt($crypttext, $decrypted, $priv);

    echo "\nDecrypted text:\n$decrypted\n\n";
}

function t3() 
{
    $config = array(
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "encrypt_key" => 1,
        "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC,
    );

    // Create the keypair
    $res = openssl_pkey_new($config);

    // Get private key
    openssl_pkey_export($res, $privkey, 'libo', $config);

    // Get public key
    $publickey = openssl_pkey_get_details($res);
    $publickey = $publickey["key"];
    //exit;

    echo "Private Key:\n$privkey\n\nPublic Key:\n$publickey\n\n";

    $cleartext = '1234 5678 9012 3456';

    echo "Clear text:\n$cleartext\n\n";

    openssl_public_encrypt($cleartext, $crypttext, $publickey);

    echo "Crypt text:\n" . base64_encode($crypttext) . "\n";

    $priv = openssl_pkey_get_private ($privkey, "libo");
    if (!$priv) {
        echo "\nGet private key fail!\n";
        exit(1);
    }
    openssl_private_decrypt($crypttext, $decrypted, $priv);

    echo "\nDecrypted text:\n$decrypted\n\n";
}

//t1();
//t2();
t3();
