<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'dblog/process/archive/em_dblog_process_archive_auth_log.class.php';

$archive = em_dblog_process_archive_auth_log::get_instance();

//$data = array('test111111', 'test2333333');
$data = 'dsdsdsd';
$archive->archive_log('test_archive.log', $data);
