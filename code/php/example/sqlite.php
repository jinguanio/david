<?php
define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';
require_once PATH_EYOUM_LIB . 'db/em_db_expr.class.php';
require_once PATH_EYOUM_LIB . 'em_transaction.class.php';

$db = em_db::factory('sqlite', array('dbname' => '/tmp/test.db'));
$db->exec('PRAGMA journal_mode=MEMORY;');
$db->exec('PRAGMA synchronous=0;');
$db->exec('PRAGMA cache_size=8000;');

//$table = <<<SQL
//CREATE TABLE IF NOT EXISTS test_table (
//    rid INTEGER NOT NULL PRIMARY KEY,
//    cmd_text INTEGER NOT NULL DEFAULT 0 
//);
//SQL;

$db->exec($table);

//$trans = new em_transaction();
//$trans->begin();
//$db->query('begin');
//$db->begin_transaction();
//$db->insert('test_table', array('cmd_text' => 2));
$expr = new em_db_expr('cmd_text+1');
$db->update('test_table', array('cmd_text' => '-' . mt_rand()), $db->quote_into('rid = ?', 1));
//$db->rollback();
//$db->commit();
//$db->query('rollback');
//$db->query('commit');
//$trans->rollback();
print_r($db->last_insert_id());

$sql = 'select * from test_table';
print_r($db->fetch_all($sql));
