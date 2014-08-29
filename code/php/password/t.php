<?php
error_reporting(E_ALL);

//echo password_hash("rasmuslerdorf", PASSWORD_DEFAULT) . "\n";
//echo password_hash("rasmuslerdorf", PASSWORD_BCRYPT) . "\n";

$password = 'rasmuslerdorf';
$hash = '$2y$07$BCryptRequires22Chrcte/VlQH0piJtjXl.0t1XkA8pw9dMXTpOq';
$hash = '$2y$10$B6OaRrIegKUw6slm0/72/OYlmvWo7lNq7PsJ84v3twqYf6puevEDq';
$hash = '$2y$10$lzW9AURBYpb1DoU3SsVnT.91ww7l13E6iYv5OoBRKlTRqInJeuuxC';
print_r(password_get_info($hash));
exit;
$algorithm = PASSWORD_BCRYPT;
$opt = [
    'cost' => 9,
    ];
echo 'old hash: ' . $hash . "\n";

$t = microtime(true);
if (password_verify($password, $hash)) {
    if (password_needs_rehash($hash, $algorithm, $opt)) {
        $hash = password_hash($password, $algorithm, $opt);
        echo 'new hash: ' . $hash . "\n";
        /* Store new hash in db */
    }
} 

if (password_verify($password, $hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}

echo "\n";
echo microtime(true) - $t;

