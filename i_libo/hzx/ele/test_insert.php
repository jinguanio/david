<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
//require_once PATH_EYOUM_LIB . 'dblog/process/insert/em_dblog_process_insert_auth_log.class.php';
//
//$insert = em_dblog_process_insert_auth_log::get_instance();
//
//$data = '(1,"","2","2",0,0,"admin","admin","test.eyou.net","test.eyou.net",1,"ac106472","localhost",1378374405,0)';
//
//$insert->insert('1378374405', $data);
require_once PATH_EYOUM_LIB . 'dblog/process/insert/em_dblog_process_insert_deliver_mail_log.class.php';

$insert = em_dblog_process_insert_deliver_mail_log::get_instance();

$data = '("2","2","0","0","admin","admin","test.eyou.net","test.eyou.net","2","2","0","0","admin","admin","test.eyou.net","test.eyou.net","\"admin\" <admin@test.eyou.net>","\"admin\" <admin@test.eyou.net>","ttttttttt","1137","201309/b/b/5232cea2268062743000_00_00-201309/1/d/5232cea2268062743000_00_00","5124c796c3f8e  7e01fbcac0edf7c1d41","","12","3","0","0","0","","ac1067ca","localhost","1379297296")';

$insert->insert('1379297296', $data);
$data = array(
	'unique_id'   => '5124c796c3f8e  7e01fbcac0edf7c1d41',
	'unique_type' => 1,
	'log_time'    => '1379297296',
	'log_node'    => 0,
);
$insert->insert_unique('5124c796c3f8e  7e01fbcac0edf7c1d41', $data);
