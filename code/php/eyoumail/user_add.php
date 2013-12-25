<?php
error_reporting(E_ALL);

require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';
require_once PATH_EYOUM_LIB . 'em_member.class.php';
require_once PATH_EYOUM_LIB . 'db/em_db_expr.class.php';
require_once PATH_EYOUM_LIB . 'em_transaction.class.php';

$domain_name = 'test.com';
$acct_name = 'libo';

$trans = new em_transaction();
$domain_key = em_member::property_factory('domain_key', array('domain_name' => $domain_name));
$user_key = em_member::property_factory('user_key', array('acct_name' => $acct_name));
$user_key->set_domain_key($domain_key);
$user = em_member::operator_factory('user', $user_key);

$attr = array(
    'password' => 'aaaaa123',
);
$property['basic'] = em_member::property_factory('user_basic', $attr);

$trans->begin();
try {
    $user->add_user($property);
    $trans->commit();
} catch (em_exception $e) {
    $trans->rollback();
    echo $e;
}

