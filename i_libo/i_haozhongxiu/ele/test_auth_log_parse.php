<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'dblog/auth_log/em_dblog_auth_log_parse.class.php';
$time = em_dblog_auth_log_parse::get_time('("4","","68708","7","0","0","xtg","xtg","xtg.com","xtg.com","1","ac1067ce","localhost","1375252957","0"');

require_once PATH_EYOUM_LIB . 'dblog/auth_log/em_dblog_auth_log_insert.class.php';

$insert = em_dblog_auth_log_insert::get_instance();
//
$str = '("4","","68708","7","0","0","xtg","xtg","xtg.com","xtg.com","1","ac1067ce","localhost","1375252957","0")' . PHP_EOL;
//var_dump(em_dblog_auth_log_insert::get_table_list());
require_once PATH_EYOUM_LIB . 'dblog/auth_log/em_dblog_auth_log_archive.class.php';

$archive = em_dblog_auth_log_archive::get_instance();

$arr = array();
for ($i = 0; $i < 100; $i++) {
	$arr[] = $str;	
}
$start_time = microtime(true);
$i = 0;
while(true) {
	$insert->insert_authlog($time, $arr);
	$archive->archive_log('test_auth_log', implode('', $arr));
	$i += 100;
	if ($i % 1000 === 0) {
		$end_time = microtime(true);
		echo 1000 / ($end_time - $start_time) . PHP_EOL;
		$start_time = $end_time;
	}
}
