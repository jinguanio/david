#!/usr/local/eyou/devmail/opt/bin/php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 批量替换权限
 * 
 * @category   eYou_Mail
 * @package    Em_
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id:$
 */

/**
 * require
 */
require_once 'functions.php';

/*
// 获取旧的acl_id
require_once 'old_acls/em_acl_struct_admin.class.php';
require_once 'old_acls/em_acl_struct_domain.class.php';
require_once 'old_acls/em_acl_struct_group.class.php';
require_once 'old_acls/em_acl_struct_maillist.class.php';

$admin = new em_acl_struct_admin;
$domain = new em_acl_struct_domain;
$group = new em_acl_struct_group;
$maillist = new em_acl_struct_maillist;

$admin_arr = $admin->_get_items();
$domain_arr = $domain->_get_items();
$group_arr = $group->_get_items();
$maillist_arr = $maillist->_get_items();

$admin_arr = array_merge($admin_arr, $domain_arr, $group_arr, $maillist_arr);
var_export(array_keys($admin_arr));
*/

// {{{  参数定义

// {{{ old

$old_acls = array (
        0 => 'pa_manage_domain',
        1 => 'pa_letter_pager',
        2 => 'pa_group_mail',
        3 => 'pa_manage_notice',
        4 => 'pa_manage_filter',
        5 => 'pa_login_ip',
        6 => 'pa_user_login_log',
        7 => 'pa_admin_login_log',
        8 => 'pa_mailing_log',
        9 => 'pa_delete_log',
        10 => 'pd_manage_domain',
        11 => 'pd_manage_user',
        12 => 'pd_manage_group',
        13 => 'pd_group_approval',
        14 => 'pd_manage_maillist',
        15 => 'pd_maillist_approval',
        16 => 'pd_letter_pager',
        17 => 'pd_group_mail',
        18 => 'pd_manage_notice',
        19 => 'pd_manage_filter',
        20 => 'pd_user_login_log',
        21 => 'pd_admin_login_log',
        22 => 'pd_mailing_log',
        23 => 'pd_delete_log',
        24 => 'pg_manage_user',
        25 => 'pg_base_info',
        26 => 'pg_extension_info',
        27 => 'pg_group_approval',
        28 => 'pg_free_users',
        29 => 'pg_group_mail',
        30 => 'pg_manage_notice',
        31 => 'pml_manage_maillist',
        32 => 'pml_manage_approval',
        33 => 'pml_view_users',
        );

// }}}
// {{{ new

$new_acls = array(
        0 => 'pa_domain',
        1 => 'pa_letter_pager',
        2 => 'pa_mass_mail',
        3 => 'pa_notice',
        4 => 'pa_filter',
        5 => 'pa_access_ip',
        6 => 'pa_user_auth_log',
        7 => 'pa_admin_auth_log',
        8 => 'pa_deliver_mail_log',
        9 => 'pa_delete_mail_log',
        10 => 'pd_domain',
        11 => 'pd_user',
        12 => 'pd_group',
        13 => 'pd_group_examine',
        14 => 'pd_maillist',
        15 => 'pd_maillist_examine',
        16 => 'pd_letter_pager',
        17 => 'pd_mass_mail',
        18 => 'pd_notice',
        19 => 'pd_filter',
        20 => 'pd_user_auth_log',
        21 => 'pd_admin_auth_log',
        22 => 'pd_deliver_mail_log',
        23 => 'pd_delete_mail_log',
        24 => 'pg_user',
        25 => 'pg_base_info',
        26 => 'pg_extension_info',
        27 => 'pg_examine',
        28 => 'pg_free_user',
        29 => 'pg_mass_mail',
        30 => 'pg_notice',
        31 => 'pml_maillist',
        32 => 'pml_examine',
        33 => 'pml_user_list',
        );

// }}}

// 数据
$diff = array_diff($old_acls, $new_acls);
//var_export($diff);

// 日志
$log = 'log/search_old_acls.log'; 

// 查找路径
$paths = array(
            '/home/libo/git/src/web/tpl/admin/tpl1'         => '*.html',
            '/home/libo/git/src/web/tpl/admin/tpl1/js'      => '*.js',
            '/home/libo/src/web/php'                        => '*.php',
            '/home/libo/src/lib/'                           => '*.php',

            '/home/libo/git/src/shell/utils'                => '*',
        );

//$paths = array('/home/libo/test' => '*.txt');

// }}}

if (is_file($log)) {
    unlink($log);
}
$fp = fopen($log, 'a+');

foreach ($paths as $path => $file_pattern) {
    $files = rglob($file_pattern, 0, $path);

    foreach ($files as $file) {
        if (false !== strpos($file, 'lang_zh.js')) {
            continue;
        }

        $file_content = file_get_contents($file);

        $key = array();
        foreach ($diff as $acl_id) {
            if (false !== strpos($file_content, $acl_id)) {
                $key[] = $acl_id;
            }
        }

        $count = count($key);
        if (0 !== $count) {
            fwrite($fp, "Search file: <$file>\n");
            fwrite($fp, "    Count: " . $count . " \n");
            fwrite($fp, "    Acl_id: " . implode(', ', $key) . " \n");
            fwrite($fp, "\n");
        }
    }
}

fclose($fp);

