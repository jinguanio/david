<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'dblog/process/parse/em_dblog_process_parse_revolute_user_log.class.php';
$parse = em_dblog_process_parse_revolute_user_log::get_instance();
$time = $parse->parse_log('("191","6","0","0","zxf","zxf","zxf.com","zxf.com","0","3","0","2","2","0","0","admin","admin","test.eyou.net","test.eyou.net","1","1","1","ac1067c8","1380009574","localhost")');

//var_dump($time);


require_once PATH_EYOUM_LIB . 'dblog/process/insert/em_dblog_process_insert_revolute_user_log.class.php';
$insert = em_dblog_process_insert_revolute_user_log::get_instance();
try {
$insert = $insert->insert(null, '("191","6","0","0","zxf","zxf","zxf.com","zxf.com","0","3","0","2","2","0","0","admin","admin","test.eyou.net","test.eyou.net","1","1","1","ac1067c8","1380009574","localhost")');

} catch (em_exception $e) {
	var_dump($e->getMessage());	
}

