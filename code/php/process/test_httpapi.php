<?php
error_reporting(E_ALL);

set_include_path('/usr/local/eyou/toolmail/app/mc/inc/conf');
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'emdata/em_emdata_client.class.php';

$client = new em_emdata_client;
$ret = $client
    ->set_module('plugin')
    ->set_action('plugin')
    ->set_params(array('a' => 'b'))
    ->run();

var_dump($ret);
