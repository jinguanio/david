<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'searchmail/em_searchmail_cfg.class.php';
require_once PATH_EYOUM_LIB . 'searchmail/em_searchmail_fields.class.php';
require_once PATH_EYOUM_LIB . 'searchmail/em_searchmail_server.class.php';
require_once PATH_EYOUM_LIB . 'searchmail/em_searchmail_client.class.php';

/* create_index

$server = new em_searchmail_server();

$fields = new em_searchmail_fields();
$fields->set_field(em_searchmail_fields::ACCT_ID, 400);
$fields->set_field(em_searchmail_fields::MAIL_ID, 2);
//$fields->decode($data);

$server->set_is_op_log(true);
echo  $server->create_index($fields, true);

*/
/* cfg
$cfg = new em_searchmail_cfg();
$server_id = $cfg->get_cfg('server_id');
echo $server_id;
*/

/* del_index

echo em_searchmail_client::del_index(400, 2);
*/
/* update_folder
*/
$server = new em_searchmail_server();

$fields = new em_searchmail_fields();
$fields->set_field(em_searchmail_fields::ACCT_ID, 152);
$fields->set_field(em_searchmail_fields::MAIL_ID, 5);
$fields->set_field(em_searchmail_fields::FOLDER_ID, 2);
$fields->set_field(em_searchmail_fields::MAILTIME, array());
$server->set_is_op_log(true);
echo  $server->update_folder($fields);

/*
$fields = new em_searchmail_fields();
$fields->set_field(em_searchmail_fields::ACCT_ID, 152);
$fields->set_field(em_searchmail_fields::MAIL_ID, 5);
$fields->set_field(em_searchmail_fields::FOLDER_ID, 2);
//$fields->set_field(em_searchmail_fields::MAILTIME, array());

$arr =  em_searchmail_client::find($fields);
print_r($arr);
*/
