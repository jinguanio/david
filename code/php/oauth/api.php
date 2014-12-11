<?php
error_reporting(E_ALL);

session_start();

echo json_encode([
    'name' => 'david',
    'age' => '35',
]);

