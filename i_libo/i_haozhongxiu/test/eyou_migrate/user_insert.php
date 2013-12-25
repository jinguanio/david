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


// {{{ create_acct

function create_acct($id, $all_num)
{
	//用户名
	$acct_name = 'user_' . $id;
	
	//域名
	$domain_name = 'test.com';

	//real name
	$real_name = 'real_name' . $id;

	//密码
	$password = 'aaaaa123';
	
	$alias_name = 'user_' . ($all_num + $id);
	
	$group_name = 'group_1';

    $extra_info = array(
			'l' => '市/县',    //    蠡县
			'st' => '省/自治区',   //   河北
			'title' => '程序员', //  程序员
			'postalcode' => '邮政编码', //     071400
			'postofficebox' => '邮政信箱', //  4578744
			'physicaldeliveryofficename' => '办公室',  // 办公室    ff
			'telephonenumber' => '011-' . mt_rand(40000, 5000000), // 电话号码 13651340538
			'facsimiletelephonenumber' => mt_rand(5000, 10000), //       66666666
			'co' => '国家(地区)',    //   中国
			'department' => '部门', //      研发部
			'company' => '亿邮', //  亿邮
			'streetaddress' => '街道', //   街道
			'wwwhomepage' => '网页',  // 网页   www.qq.com
			'ipphone' => 'IP电话', // IP电话 77777777
			'homephone' => '010-' . mt_rand(40000, 5000000), //      010-88888888
			'mobile' => '131' . time(),//  13651340538
			'pager' => '寻呼机', //  6666666
	 );

	return array(
		'acct_name' => $acct_name,
		'domain_name' => $domain_name,
		'real_name' => $real_name,
		'password' => $password,
		'alias_name' => $alias_name,
		'group_name' => $group_name,
		//'extra_info' => json_encode($extra_info),
	);
}

// }}}
// {{{ insert

function insert()
{
	global $db;
	$acct_num = 10;
	$attr = array();

	for ($i = 0; $i < $acct_num; $i++ ) {
		$attr = create_acct($i, $acct_num);
		$db->insert('user_sync', $attr);
	}
	
}

// }}}
// {{{ delete

function delete()
{
	global $db;
	$db->delete('user_sync', '1=1');
}

// }}}

$opts = getopt('ads');
if (isset($opts['a'])) { //插入
	insert();
} 

if (isset($opts['d'])) {
	delete();
} 

