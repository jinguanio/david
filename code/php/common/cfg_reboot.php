<?php
// reboot 是需要重启邮件系统的配置
// read_only 是不允许 web 页面修改的系统配置
$reboot =
// {{{
array (
    'db' => 
    array (
        0 => 'db_type',
        1 => 'db_num',
        2 => 'db_name',
        3 => 'db_mysql_host',
        4 => 'db_mysql_port',
        5 => 'db_mysql_user',
        6 => 'db_mysql_pass',
        7 => 'db_mysql_sock',
        8 => 'dbumi_type',
        9 => 'dbumi_mysql_user',
        10 => 'dbumi_mysql_pass',
        11 => 'dbumi_mysql_dsn',
    ),
    'memcache' => 
    array (
        0 => 'memcache_session',
        1 => 'memcache_fix',
        2 => 'memcache_hot',
    ),
    'rtmp' => 
    array (
        0 => 'rtmp_host',
        1 => 'rtmp_port',
    ),
    'filed' => 
    array (
        0 => 'filed_host',
        1 => 'filed_port',
        2 => 'filed_sock',
    ),
    'searchd' => 
    array (
        0 => 'search_host',
        1 => 'search_port',
    ),
    'server' => 
    array (
        0 => 'server_me',
        1 => 'server_name',
        2 => 'server_url',
        3 => 'self_name',
    ),
    'password_encode' => 
    array (
        0 => 'password_encode_algo',
        1 => 'password_encode_em_key',
        2 => 'password_encode_crypt_key',
        3 => 'password_encode_crypt_mode',
        4 => 'password_encode_crypt_iv',
    ),
    'mail_deliver' => 
    array (
        0 => 'ignore_duplicate_mail',
        1 => 'ignore_duplicate_mail_duration',
    ),
    'examine_deliver' => 
    array (
        0 => 'group_examine_queue_timeout',
        1 => 'group_examine_header_private_key',
    ),
    'restrict' => 
    array (
        0 => 'restrict_acct_name_len_min',
        1 => 'restrict_acct_name_len_max',
        2 => 'restrict_domain_name_len_min',
        3 => 'restrict_domain_name_len_max',
        4 => 'restrict_user_password_len_min',
        5 => 'restrict_user_password_len_max',
        6 => 'restrict_user_quota_max',
        7 => 'restrict_user_attach_size_max',
        8 => 'restrict_user_rcpt_num_max',
        9 => 'restrict_user_rcpt_size_max',
        10 => 'restrict_user_upload_size_max',
        11 => 'restrict_folder_name_len_min',
        12 => 'restrict_folder_name_len_max',
    ),
    'system' => 
    array (
        0 => 'error_output_mode',
        1 => 'syslog_option',
        2 => 'syslog_facility',
        3 => 'syslog_priority',
        4 => 'debug',
        5 => 'debug_writer',
        6 => 'stress_testing_level',
        7 => 'stress_testing_user',
        8 => 'timezone',
        9 => 'session_lifetime',
        10 => 'lang',
        11 => 'lang_list',
        12 => 'innerapi_user',
        13 => 'member_property_cache_type',
        14 => 'hash_level_tmp',
        15 => 'nginx_upload_url',
        16 => 'pop_acct_check_set',
        17 => 'timemail_queue',
        18 => 'tempfile_memory',
        19 => 'reserve_acct_name',
        20 => 'phpdaemon_lifetime',
        21 => 'lock_type',
        22 => 'lock_emls_server',
        23 => 'lock_timeout_mi_in',
        24 => 'lock_timeout_nb_in',
        25 => 'lock_timeout_eq_scan',
        26 => 'maildecode_html_formator',
    ),
    'auth' => 
    array (
        0 => 'password_attempts_lock_ip',
        1 => 'password_attempts_unrestricted_ip',
    ),
    'pushmail' => 
    array (
        0 => 'activesync_title',
        1 => 'activesync_remote_wipe',
    ),
    'migrate' => 
    array (
        0 => 'user_migrate_set',
    ),
    'event' => 
    array (
        0 => 'event_mail_index_reserve_time',
        1 => 'event_trigger_on_async_after_receive_mail',
        2 => 'event_trigger_on_async_after_deliver_remote_mail',
        3 => 'event_trigger_on_async_after_draft_mail',
        4 => 'event_trigger_on_async_after_update_folder_stat',
    ),
    'implements' => 
    array (
        0 => 'imptype_smssend',
        1 => 'imptype_smsnotify',
        2 => 'imptype_authreg',
        3 => 'imptype_uminodeid',
    ),
    'gearman' => 
    array (
        0 => 'gmw_innerapi',
        2 => 'gmw_rtmp_auth',
        4 => 'gmw_timemail_send',
        6 => 'gmw_notebook_remind',
        8 => 'gmw_member_iteration',
        10 => 'gmw_archive_log',
        12 => 'gmw_clear_tempfile',
        14 => 'gmw_async_event',
        16 => 'gmw_event_after_receive_mail',
        18 => 'gmw_event_after_deliver_remote_mail',
        20 => 'gmw_event_after_draft_mail',
        22 => 'gmw_event_after_change_folder',
        24 => 'gmw_event_after_update_folder_stat',
        26 => 'gmw_pop_acct',
        28 => 'gmw_searchmail',
        30 => 'gmw_sms',
        32 => 'gmw_user_migrate',
    ),
    'mta' => 
    array (
        0 => 'mta_additional_locale_language',
        1 => 'smtp_printauth_when_ehlo',
        2 => 'smtp_rcpt_size',
        3 => 'smtp_auth_check_local',
        4 => 'smtp_auth_check_domain',
        5 => 'smtp_auth_check_user',
        6 => 'smtp_auth_check_body',
        7 => 'smtp_mailfrom_check_domain',
        8 => 'smtp_relayhost',
        9 => 'smtp_rcpt_num',
        10 => 'local_spam_value',
        11 => 'local_spam_key',
        12 => 'local_ignore_sender',
        13 => 'remote_smtproute',
        14 => 'remote_helo_host',
        15 => 'remote_relay_domain',
        16 => 'remote_enable_replace_domain',
        17 => 'remote_replace_domain',
        18 => 'remote_deliver_event',
    ),
    'plugin_notebook' => 
    array (
        0 => 'plugin_notebook_reminder_queue',
    ),
);
// }}}

$read_only = 
    // {{{
    array (
        'node' => 
        array (
            0 => 'node_id',
            1 => 'node_center_id',
            2 => 'node_name',
            3 => 'node_server',
            4 => 'node_umi',
            5 => 'node_rcd_port',
            6 => 'node_module_smtp',
            7 => 'node_module_webmail_api',
            8 => 'node_module_pushmail',
            9 => 'node_module_pop',
            10 => 'node_module_pop_acct',
            11 => 'node_module_time_mail_deliver',
            12 => 'node_module_notebook_remind',
            13 => 'node_module_member_iteration',
            14 => 'node_module_statistics',
            15 => 'node_module_search',
            16 => 'node_module_filed',
            17 => 'node_module_webmail_api_proxy',
            18 => 'node_module_pushmail_proxy',
            19 => 'node_module_db',
            20 => 'node_module_mdb',
            21 => 'node_module_memcache_session',
            22 => 'node_module_memcache_fix',
            23 => 'node_module_memcache_hot',
            24 => 'node_module_gearman_mta',
            25 => 'node_module_gearman_php',
        ),
        'db' => 
        array (
            0 => 'db_type',
            1 => 'db_num',
            2 => 'db_name',
            3 => 'db_mysql_host',
            4 => 'db_mysql_port',
            5 => 'db_mysql_user',
            6 => 'db_mysql_pass',
            7 => 'db_mysql_sock',
            8 => 'dbumi_type',
            9 => 'dbumi_mysql_user',
            10 => 'dbumi_mysql_pass',
            11 => 'dbumi_mysql_dsn',
        ),
        'memcache' => 
        array (
            0 => 'memcache_session',
            1 => 'memcache_fix',
            2 => 'memcache_hot',
        ),
    );
// }}}

echo '<pre>'. var_export($reboot, true).'</pre>';
