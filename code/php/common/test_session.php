<?php
error_reporting(E_ALL);

session_name('libo');
session_id('liboid');
session_start();
$_SESSION['name'] = 'libo_name';
session_write_close();

session_name('bnn');
session_id('bnnid');
session_start();
$_SESSION['name'] = 'bnn_name';
session_write_close();

session_name('bnn');
session_id('bnnid');
session_start();
var_dump($_SESSION['name']);
session_write_close();

session_name('libo');
session_id('liboid');
session_start();
var_dump($_SESSION['name']);

