<?php
declare(ticks = 1);
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'pgmond/em_pgmond.class.php';
$em_pgmond = new em_pgmond('127.0.0.1:8649');

var_dump($em_pgmond->run());
