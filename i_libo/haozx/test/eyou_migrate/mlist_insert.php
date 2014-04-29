<?php
define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';
$ad_db_opt = array(
		//'host'     => '172.16.100.224',
		//'port'     => '3306',
		'dbname' => 'eyou_ad_sync',
		'username' => 'root',
		//'password' => 'aaaaa',
		'unix_socket' => '/usr/local/eyou/mail/run/em_mysql.sock',
		);

$db = em_db::singleton(null, $ad_db_opt);


// {{{ create_mlist

function create_mlist($id)
{
	$maillist_name = 'group_' . $id;
	
	//域名
	$domain_name = 'test.com';

	return array(
		'domain_name' => $domain_name,
		'maillist_name' => $maillist_name,
		'maillist_cnname' => $maillist_name,
	);
}

// }}}
// {{{ insert

function insert()
{
	global $db;
	$acct_num = 1;
	$attr = array();

	for ($i = 0; $i < $acct_num; $i++) {
		$attr = create_mlist($i);
		$db->insert('maillist_sync', $attr);
	}
}

// }}}
// {{{ delete

function delete()
{
	global $db;
	$db->delete('maillist_sync', '1=1');
}

// }}}

$opts = getopt('ad');
if (isset($opts['a'])) { //插入
	insert();
} 

if (isset($opts['d'])) {
	delete();
} 

