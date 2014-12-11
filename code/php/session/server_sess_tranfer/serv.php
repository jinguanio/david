<?php
session_save_path('/tmp/sess');

//session_name('libo');
session_id($_GET['id']);
session_start();
//$_SESSION['file'] = __FILE__;
//session_write_close();

echo "Cookie:\n";
print_r($_COOKIE);

echo "Session:\n";
print_r($_SESSION);
//header('Location: http://172.16.100.110:3333?id=' . $_GET['id']);

