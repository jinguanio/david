<?php
define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

$db = em_db::singleton();
$select = $db->select()
    ->from(array('a' => 'domain_key'), array('domain_id', 'domain_name'))
    ->join_inner(array('b' => 'domain_basic'), 'a.domain_id=b.domain_id', array('init_time', 'expiration_time'))
    ->where('a.domain_name=\'lb.com\'');

print_r($db->fetch_row($select));
