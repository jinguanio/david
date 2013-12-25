<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_member.class.php';
require_once PATH_EYOUM_LIB . 'member/em_member_mail_recall.class.php';

$domain_name = 'hzx.com';
$acct_name = 'hzx';
$domain_key_property = em_member::property_factory('domain_key', array('domain_name' => $domain_name));
$domain = em_member::operator_factory('domain', $domain_key_property);
$domain->get_operator('key')->process_key();
$domain_key_property = $domain->get_domain_key_property();
$user_key_property = em_member::property_factory('user_key', array('acct_name' => $acct_name));
$user_key_property->set_domain_key($domain_key_property);
$user = em_member::operator_factory('user', $user_key_property);

$user->get_operator('key')->process_key();

$recall = new em_member_mail_recall($user);
$recall->recall_by_log('e5b17bdd66be339988919ee70ca4e348');
