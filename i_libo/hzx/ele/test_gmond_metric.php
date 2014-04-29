<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'implements/em_implements_helper.class.php';
require_once PATH_EYOUM_LIB. 'em_exception.class.php';
require_once PATH_EYOUM_LIB. 'em_log.class.php';
require_once PATH_EYOUM_LIB. 'daemon/em_daemon_log.class.php';
$proc_class_name = em_implements_helper::import('process', 'gmond_metric');
$proc = new $proc_class_name();

$options = array(
		'src' => PATH_EYOUM_LOG . 'test_auth.log',
		'own' => EYOUM_EXEC_UID,
		);
$writer = em_log::writer_factory('file', $options);
$log = new em_daemon_log($writer);
$log->set_debug(7);

$proc->set_log($log);
try {
	$proc->run();
} catch (em_exception $e) {
	var_dump($e);
}
