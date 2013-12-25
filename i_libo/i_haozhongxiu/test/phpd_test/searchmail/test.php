<?php
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

$dbname = PATH_EYOUM_DATA_SEARCHMAIL . '2013/01/8/2/400_20130109.db';
$db = em_db::factory('sqlite', array('dbname' => $dbname));

// {{{ function _init_sqlite()

function init_sqlite()
{
	global $db;
	$tables = $db->list_tables();

	if (in_array('search_mail', $tables)) {
		return ;
	}

	// 不存在 search_mail 表创建
	$sql = <<<SQL
		CREATE VIRTUAL TABLE search_mail USING FTS4 (
			mail_id,
			folder_id,
			subject,
			content,
			mail_from,
			mail_to,
			attach_name,
			attach_type,
			mailtime
		);
SQL;
	try {
		$db->exec($sql);
	} catch (exception $e) { // 可能另外一个进程已经创建
		$tables = $db->list_tables();
		if (!in_array('search_mail', $tables)) {
			throw $e;
		}
	}
}

    // }}}
// {{{ create_index

function create_index($id)
{
	$mail_id = $id;
	
	$folder_id = $id + rand(0, 10);

	$subject = 'test' . $id;

	$content = 'content_test'. $id;

	$mail_from = 'user_1<user_1@hzx.com>';
	$mail_to = 'user_2<user_1@hzx.com>';

	//邮件时间
	$mailtime = time() - 86400 * (rand(0,30) - rand(0, 30)) ;

	return array(
		'mail_id' => $mail_id,
		'folder_id' => $folder_id,
		'subject' => $subject,
		'content' => $content,
		'mail_from' => $mail_from,
		'mail_to' => $mail_to,
		'mailtime' => $mailtime,
	);
}

// }}}
// {{{ insert

function insert()
{
	global $db;
	init_sqlite();

	$acct_num = 10;
	$attr = array();

	for ($i = 0; $i < $acct_num; $i++) {
		$attr = create_index($i);
		try {
			$db->insert('search_mail', $attr);
		} catch (exception $e) {
			echo $e->getMessage() . PHP_EOL;	
		}
	}
}

// }}}
// {{{ delete

function delete()
{
	global $db;
	$db->delete('search_mail', '1=1');
}

// }}}

$opts = getopt('ad');
if (isset($opts['a'])) { //插入
//	insert_master();
	insert();
} 

if (isset($opts['d'])) {
	delete();
} 


