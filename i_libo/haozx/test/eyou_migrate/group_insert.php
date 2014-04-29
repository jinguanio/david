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


// {{{ create_group

function create_group($id, $parent_group = '')
{
	$group_name = 'group_' . $id;
	
	//域名
	$domain_name = 'test22222.com';

	return array(
		'domain_name' => $domain_name,
		'group_name' => $group_name,
		'group_cnname' => $group_name,
		'parent_group_name' => $parent_group,
	);
}

// }}}
// {{{ insert

function insert()
{
	global $db;
	$acct_num = 2;
	$pnum = 1;
	$pnum2 = 0;
	$attr = array();

	$parent_group = array();
	for ($i = 0; $i < $pnum; $i++) {
		$attr = create_group($i);
		$parent_group[] = $attr['group_name'];
		$db->insert('group_sync', $attr);
	}

	for ($i = $pnum; $i <= $pnum + $pnum2; $i++) {
		$attr = create_group($i, $parent_group[mt_rand(0, $pnum-1)]);
		$parent_group2[$i] = $attr['group_name'];
		$db->insert('group_sync', $attr);
	}
/*
	for ($i = $pnum2 + $pnum; $i < $acct_num; $i++) {
		$attr = create_group($i, $parent_group2[mt_rand($pnum, $pnum + $pnum2-1)]);
		$db->insert('group_sync', $attr);
	}*/
	
}

// }}}
// {{{ delete

function delete()
{
	global $db;
	$db->delete('group_sync', '1=1');
}

// }}}

$opts = getopt('ads');
if (isset($opts['a'])) { //插入
	insert();
} 

if (isset($opts['d'])) {
	delete();
} 

