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
    openssl_pkey_export($res, $privkey, 'libo', $config);
    var_dump($privkey);
    exit;

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

function t3()
{
    $pub_key_file = __DIR__ . '/keys/openssl_sign-public_key.pem';
    $priv_key_file = __DIR__ . '/keys/openssl_sign-private_key.pem';
    $new_priv_key_file = __DIR__ . '/keys/openssl_sign-private_key_new.pem';

    $pub_key = file_get_contents($pub_key_file);
    $priv_key = file_get_contents($priv_key_file);

    $cleartext = '1234 5678 9012 3456';

    openssl_public_encrypt($cleartext, $crypttext, $pub_key);
    echo "Crypt text:\n" . base64_encode($crypttext) . "\n";

    openssl_private_decrypt($crypttext, $decrypted, $priv_key);
    echo "\nDecrypted text:\n$decrypted\n\n";

    $priv = openssl_pkey_get_private($priv_key);
    openssl_pkey_export($priv, $new_priv, 'libo');
    file_put_contents($new_priv_key_file, $new_priv);

    $priv = openssl_pkey_get_private($new_priv, 'libo');
    openssl_private_decrypt($crypttext, $decrypted, $priv_key);
    echo "\nDecrypted text:\n$decrypted\n\n";
}

// 公私钥加密解密
//t1();

// 私钥添加密码后进行加密解密
t2();

// 验证不同私钥解密过程是否相同
//t3();

