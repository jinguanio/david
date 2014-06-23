<?php
error_reporting(E_ALL);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'monitor/em_monitor_cache_agent.class.php';
$__cache = new em_monitor_cache_agent();

$weight = [
    'config' => 0.02,
    'plugin' => 0.02,
    'post' => 0.84,
    'noop' => 0.14,
    ];

$job_id = md5(time() . mt_rand(0, 99999));
$job_ts = time();
$hid = '197554017';
$hname = '197554017__libo__process.com';
$parter_id = '5c3c7c9dc502b4328a61c462ca90cb46';

function insert_queue($total = 10000)
{
    // {{{ data
    
    $tpl_data = [
        'post' => [
            'type' => 'post',
            'parter_id' => $parter_id,
            'hid' => $hid,
            'hname' => $hname,
            'pname' => 'http_svr',
            'job' => [ $job_id, $job_ts ],
            'data' => [
                'res' => '0',
                'act' => json_encode([
                    'mail' => [ 'succ' => [], 'fail' => ['a@a.com', 'b@b.com']],
                    'sms' => [ 'succ' => [], 'fail' => [1581234656, 13243458658]],
                ]),
                'snap' =>  '/tmp/xml',
                'level' => 'ok',
                'repeat' => 5,
                'ret' => 'str',
                'title' => 'HTTP SVR OK',
                'summary' => '2/2 http check succeed',
                'detail' => json_encode([
                    [ 'color' => 'red', 'title' => '标题1', 'val' => 'intel' ],
                ]),
                'auto' => json_encode([
                    [ 'color' => 'red', 'title' => '标题1', 'val' => 'intel' ],
                ]),
                'extra' => '',
            ]
        ],
        'plugin' => [
            'type' => 'plugin',
            'parter_id' => $parter_id,
            'hid' => $hid,
            'hname' => $hname,
            'data' => [
                [
                    'name' => 'http_svr',
                    'comment' => 'HTTP Service Check',
                    'freq' => '3min',
                    'timeout' => '2min',
                    'errnum' => '2',
                    'snap' => 'none',
                    'mail' => 'crit warn',
                    'sms' => 'warn',
                    'post' => 'unkn tmout warn crit',
                    'auto' => 'tmout',
                    'attsnap' => '0',
                    'debug' => '0', 
                    'enable' => '1', 
                    'locale' => 'zh_CN', 
                    'mail_rec' => 'zhangguangzheng@eyou.net libo@eyou.net',
                    'sms_rec' => '123456789 987654321',
                    'handler' => 'default_handler',
                    'udef' => json_encode([
                        [ 'flag' => 'addr_port', 'title' => '端口', 'val' => 'http:127.0.0.1:80 https:127.0.0.1:443' ],
                    ]),
                ]
            ]
        ],
        'config' => [
            'type' => 'config',
            'parter_id' => $parter_id,
            'hid' => $hid,
            'hname' => $hname,
            'data' => [
                'global' => json_encode([ 
                    'scan_interval' => 5, 
                    'attach_ini_mail' => 1, 
                    'sysload_uplimit' => 30, 
                    'max_kidsnum' => 50, 
                    'plugin_maxlen' => 65536, 
                    'handler_maxlen' => 32768, 
                    'notify_onmisform' => 1,
                    'locale' => 'zh_CN',
                ]),
                'default' => json_encode([ 
                    'enable' => '0',
                    'comment' => 'Eminfo Plugin',
                    'freq' => '3min',
                    'timeout' => '2min',
                    'errnum' => '2',
                    'snap' => 'all',
                    'mail' => 'all',
                    'sms' => 'all',
                    'post' => 'all',
                    'auto' => 'all',
                    'attsnap' => '0',
                    'debug' => '0',
                    'mail_rec' => 'zhangguangzheng@eyou.net root_bbk@126.com',
                    'sms_rec' => '123456789 987654321',
                    'handler' => 'default_handler',
                    'locale' => 'zh_CN',
                ]),
                'sendmail' => json_encode([ 
                    'smtp_server' => 'smtp.sina.com.cn',
                    'smtp_server_port' => 25,
                    'auth_user' => 'eyou_uetest@sina.com.cn',
                    'auth_pass' => 'eyou-uetest',
                    'timeout' => 10,
                    'charset' => 'utf8',
                ]),
                'postlog' => json_encode([ 
                    'post_server' => 'ota.eyou.net',
                    'post_server_port' => 1218,
                    'post_port_type' => 'tcp',
                    'post_timeout' => 10,
                    'post_max_length' => 50000,
                    'post_debug' => 1,
                ]),
                'takesnap' => json_encode([]),
                'clear_overdue' => json_encode([ 
                    'frequency'             => '10min',
                    'exec_tmout'            => '5min',
                    'tmpfile_reserve_time'  => 7,
                    'logfile_reserve_time'  => 180,
                    'snapfile_reserve_time' => 7,
                    'snapdir_maxsize'       => 4096,
                ]),
                'log_rotate' => json_encode([ 
                    'frequency'  => '10min',
                    'exec_tmout' => '5min',
                    'force_size_uplimit' => 1024,
                ]),
                'self_check' => json_encode([ 
                    'frequency'  => '10min',
                    'exec_tmout' => '5min',
                ]),
                'iam_alive' => json_encode([ 
                    'frequency'  => '10min',
                    'exec_tmout' => '5min',
                ]),
                'check_remote' => json_encode([ 
                    'frequency'  => '10min',
                    'exec_tmout' => '5min',
                ]),
                'check_remote' => json_encode([ 
                    'frequency'  => '1hour',
                    'exec_tmout' => '10min',
                ]),
            ],
        ],
        'noop' => [
            'type' => 'noop',
            'parter_id' => $parter_id,
            'hid' => $hid,
            'hname' => $hname,
            'job' => [ $job_id, $job_ts ],
            'data' => [
                'interval' => 150,
            ]
        ],
    ];

    // }}}

    foreach ($weight as $k => $w) {
        $num = $w * $total;
        for ($i = 0; $i < $num; $i++) {
            $__cache->set_agent_queue($tpl_data[$k]);
        }
    }

    echo "+Ok, insert redis queue.\n";
}

function fork_proc($num = 5)
{
}

function conn_redis()
{
    $redis = new Redis();
    $redis->connect('localhost', '6379');
    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    return $redis;
}

insert_queue();
fork_proc();

