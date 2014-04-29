<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'dblog/iterator/em_dblog_auth_log_iterator.class.php';

$iterator = new em_dblog_auth_log_iterator('/tmp/a.txt');
$iterator->set_offset(10);
foreach ($iterator as $value) {
	var_dump($value);	
	sleep(1);
}
