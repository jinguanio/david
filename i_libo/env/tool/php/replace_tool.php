#!/usr/local/eyou/devmail/opt/bin/php
<?php
exit('must open manually.');
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

require_once '/home/libo/git/src/lib/acl/struct/em_acl_struct_admin.class.php';
require_once '/home/libo/git/src/lib/acl/struct/em_acl_struct_domain.class.php';
require_once '/home/libo/git/src/lib/acl/struct/em_acl_struct_group.class.php';
require_once '/home/libo/git/src/lib/acl/struct/em_acl_struct_maillist.class.php';

// 获取替换模版
$admin = new em_acl_struct_admin;
$domain = new em_acl_struct_domain;
$group = new em_acl_struct_group;
$maillist = new em_acl_struct_maillist;

$admin_acls = array_keys($admin->get_privileges(''));
$domain_acls = array_keys($domain->get_privileges(''));
$group_acls = array_keys($group->get_privileges(''));
$maillist_acls = array_keys($maillist->get_privileges(''));

$acls_all = array();
$acls_all = array_merge($acls_all, $admin_acls, $domain_acls, $group_acls, $maillist_acls);

$new_acls_id = array(
            'pa_domain',
            'pa_letter_pager',
            'pa_mass_mail',
            'pa_notice',
            'pa_filter',
            'pa_access_ip',
            'pa_user_auth_log',
            'pa_admin_auth_log',
            'pa_deliver_mail_log',
            'pa_delete_mail_log',
            'pd_domain',
            'pd_user',
            'pd_group',
            'pd_group_examine',
            'pd_maillist',
            'pd_maillist_examine',
            'pd_letter_pager',
            'pd_mass_mail',
            'pd_notice',
            'pd_filter',
            'pd_user_auth_log',
            'pd_admin_auth_log',
            'pd_deliver_mail_log',
            'pd_delete_mail_log',
            'pg_user',
            'pg_base_info',
            'pg_extension_info',
            'pg_examine',
            'pg_free_user',
            'pg_mass_mail',
            'pg_notice',
            'pml_maillist',
            'pml_examine',
            'pml_user_list',
        );

// {{{  参数定义

// 替换模版
$replace_pattern = array();
foreach ($acls_all as $index => $value) {
    $replace_pattern[$value] = $new_acls_id[$index];
}
//$replace_pattern = array('aaa' => 'xxx', 'bbb' => 'qqq');
//var_export($replace_pattern);

// 替换路径
$paths = array(
            '/home/libo/git/src/web/tpl/admin/tpl1'         => '*.html',
            '/home/libo/git/src/web/tpl/admin/tpl1/js'      => '*.js',
            '/home/libo/src/web/php'                        => '*.php',
            '/home/libo/src/lib/acl'                        => '*.php',
        );
//$paths = array('/home/libo/test' => '*.txt');

$log = 'log/replace.log'; // 替换日志
//$operator = 'restore'; // 操作标志: replace or restore 
$operator = 'replace'; // 操作标志: replace or restore 

// }}}

if (is_file($log)) {
    unlink($log);
}
$fp = fopen($log, 'a+');
fwrite($fp, date('Y-m-d H:i:s', time()) . "\n");

foreach ($paths as $path => $file_pattern) {
    $files = rglob($file_pattern, 0, $path);

    foreach ($files as $file) {
        if (!is_file($file)) {
            fwrite($fp, "<$file> is not handled.\n");
            continue;
        }

        if ('restore' === $operator) {
            copy($file . '.libo', $file);
        } else {
            $file_content = file_get_contents($file);

            fwrite($fp, "Modify file: <$file>\n");
            $replace_num = 0;
            foreach ($replace_pattern as $old_value => $new_value) {
                if (false !== strpos($file_content, $old_value)) {
                    $file_content = str_replace($old_value, $new_value, $file_content);
                    $replace_num++;
                }
            }
            fwrite($fp, "    Count: $replace_num \n");
            fwrite($fp, "\n");

            // var_export($file_content);
            // shell: find -name '*.libo' | xargs rm -rf
            if (0 !== $replace_num) {
                copy($file, $file . '.libo');
                file_put_contents($file, $file_content);
            }
        }
    }

    if ('restore' === $operator) {
        fwrite($fp, "Restore successfully.");
    }
}

fclose($fp);
