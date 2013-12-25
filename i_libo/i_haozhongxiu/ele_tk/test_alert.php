<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_alert.class.php';

$test = '23';
var_dump(em_alert::get_threshold($test));
