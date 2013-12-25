<?php
error_reporting(E_ALL);

define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_member.class.php';
require_once PATH_EYOUM_LIB . 'em_condition.class.php';
 
// setting
$domain_id = 3; $acct_id = 12;
$domain_name = 'lb.com'; $acct_name = 'lb';
$config_list = array();

// operator
try {
    $domain_key = em_member::property_factory('domain_key', array('domain_id' => $domain_id));
    $user_key = em_member::property_factory('user_key', array('acct_id' => $acct_id));
    $domain_operator = em_member::operator_factory('domain', $domain_key);
    $user_operator = em_member::operator_factory('user', $user_key);
    $domain_operator->get_operator('key')->process_key();
    $user_operator->get_operator('key')->process_key();
    var_dump($user_operator->get_operator('config')->get_user_config('alert_change_password_set'));
} catch (Exception $e) {
    var_dump($e->getMessage());
}

try {
    $domain_key = em_member::property_factory('domain_key', array('domain_name' => 'lb.com'));
    $user_key = em_member::property_factory('user_key');
    $user_key->set_domain_key($domain_key);
    $user_operator = em_member::operator_factory('user', $user_key);
    var_dump($user_operator->get_operator('config')->get_user_config('alert_change_password_set'));
} catch (Exception $e) {
    print_r($e->getMessage());
}
exit;


/*
// config
echo PHP_EOL, str_repeat('=', 10) . ' config ' . str_repeat('=', 10), PHP_EOL;
try {
    $condition = em_condition::factory('member:operator', 'user_config:get_config');

    $condition->set_member_id($acct_id);
    $condition->set_scope(em_member::USER_CONFIG_SCOPE_USER);
    
    //$condition->set_member_id($domain_id);
    //$condition->set_scope(em_member::USER_CONFIG_SCOPE_DOMAIN);
    //$condition->set_scope(em_member::USER_CONFIG_SCOPE_SYSTEM);
    
    $condition->set_config_name('forwarding_num');
    var_dump($user_operator->get_operator('config')->get_config($condition));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
 */

/*
// map
echo PHP_EOL, str_repeat('=', 10) . ' map ' . str_repeat('=', 10), PHP_EOL;
try {
    $config_name = 'filter_num';
    $config_property = em_member::property_factory('user_config');
    $config_map = $config_property->map();
    //var_export(count($config_map->get_map_keys()['key_name']));
    //var_export(array_keys($config_map->get_map()));
    echo 'scope: ', var_export($config_map->get_scope($config_name), true), PHP_EOL;
    echo 'default: ', var_export($config_map->get_default($config_name), true), PHP_EOL;
    echo 'type: ', var_export($config_map->get_type($config_name), true), PHP_EOL;
    echo 'category: ', var_export($config_map->get_category($config_name), true), PHP_EOL;
} catch (Exception $e) {
    var_dump($e->getMessage());
}
 */

