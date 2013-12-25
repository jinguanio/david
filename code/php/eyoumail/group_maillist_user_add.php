<?php
error_reporting(E_ALL);

require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_member.class.php';
require_once PATH_EYOUM_LIB . 'em_examine.class.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

$module = 'maillist';

$domain_name    = 'test.com';   // id: 14
$group_name     = 'test';       // id: 22143
$maillist_name  = 'mlist_0';    // id: 22105
$acct_name      = $group_name;

$user = array(
    '22096' => 'user_1',
    '22097' => 'user_2',
    '22098' => 'user_3',
);

function get_sql()
{
    global $profile;

    echo "\nSQL: \n";
    $profile_query = $profile->get_query_profiles();
    foreach ($profile_query as $pq) {
        echo $pq->get_query_sql() . "\n";

        $param = $pq->get_bound_params();
        if (!empty($param)) {
            print_r($param) . "\n";
        }

        echo "=============================\n";
    }
}

$db = em_db::singleton();
$profile = $db->get_profile();
$profile->set_enabled(true);

$domain_key = em_member::property_factory('domain_key', array('domain_name' => $domain_name));

try {
    switch ($module) {
    case 'maillist':
        $group_key = em_member::property_factory('group_key', array('acct_name' => $acct_name));
        $group_key->set_acct_type(em_member::ACCT_TYPE_MAIL_LIST);
        $group_key->set_domain_key($domain_key);
        $group = em_member::operator_factory('group', $group_key);
        $group->get_operator('key')->process_key();

        $examine = em_examine::factory('subscribe');
        foreach ($user as $u) {
            $examine->add_subscriber($group, $u . '@' . $domain_name);
        }
        echo "+OK.\n";
        break;

    case 'group':
        $group_key = em_member::property_factory('group_key', array('acct_name' => $acct_name));
        $group_key->set_acct_type(em_member::ACCT_TYPE_GROUP);
        $group_key->set_domain_key($domain_key);
        $group = em_member::operator_factory('group', $group_key);
        $group->get_operator('key')->process_key();

        $property = array();
        foreach ($user as $aid => $u) {
            $property[] = em_member::property_factory('group_user_local', array('acct_id' => $aid));
        }

        $group->get_operator('user_local')->add_user_local($property);
        echo "+OK.\n";
        break;
    }

    get_sql();
} catch (em_exception $e) {
    echo $e;
}

