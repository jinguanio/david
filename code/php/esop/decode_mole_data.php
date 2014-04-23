<?php
error_reporting(E_ALL);

$str = 'My0CGGIPNnr0QkflGRCV0o6Q+m5QsejxxpZ6DD2dU7ujr41nO4QsFrMNAK8mpPtckDDPz41dGdjpIG56vTzcobwMcmPi8uiciZZRyEFqv1TCnuGz2sqxCip/OndoJpzwhxW9gRToQYtgSc3o2sPz67KFC42luKYaNOAxYT1fBRxKPaDr1oSgevFQsuAXi1hYqoX04s9bX+ciwT9JJe9NtIA=';

$data = base64_decode($str); 
$flag = substr($data, 0, 1); 
$is_encrypt = $flag & 2;                                             
$is_zip = $flag & 1;
$private_key = '1234567812345678';
$iv = str_repeat(chr(0), 16);

$res_data = substr($data, 1);
if ($is_encrypt) {
    $res_data = openssl_decrypt($res_data, 'aes-128-cbc', $private_key, true, $iv);
}

if ($is_zip) {
    $res_data = gzdecode($res_data);
}

print_r(json_decode($res_data, true));

