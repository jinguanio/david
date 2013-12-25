<?php
error_reporting(E_ALL);

require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';
require_once PATH_EYOUM_LIB . 'db/em_db_expr.class.php';
require_once PATH_EYOUM_LIB . 'em_transaction.class.php';

$opt = array(
    'dbname' => 'eyou_ad_sync',
    'username' => 'root',
    'unix_socket' => '/usr/local/eyou/mail/run/em_mysql.sock',
);
$tbl = 'group_sync';
$attr['sync_status'] = new em_db_expr('sync_status+1');
//$attr['sync_status'] = 1;
$where = 'domain_name=\'0\' AND group_name=\'0\'';

try {
    $db = em_db::factory(null, $opt);

    $db->begin_transaction();

    $ret = $db->update($tbl, $attr, $where);
    echo "update result: {$ret}\n";

    $query = $db->select()->from($tbl);
    $data = $db->fetch_all($query);

    echo "return data: ";
    print_r(count($data));
    sleep(10);

    $db->commit();
} catch (Exception $e) {
    echo $e;
}

