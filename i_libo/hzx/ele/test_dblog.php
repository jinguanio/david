<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';


$db = em_db::singleton_authlog();

var_dump($db->exec('select version()'));
