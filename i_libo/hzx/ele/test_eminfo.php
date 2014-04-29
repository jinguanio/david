<?php

// {{{ post
$post_log = '{"type": "post", "parter_id" : 15,"hid": "614b1c188295b488c030163f1d27d6e7","hname" : "front-1_sem.tsinghua.edu.cn","pname" : "http_svr","job_type": "0/1","job":["job_id","job_ts"],
"data": {"res": "0","act":{ "mail":{"succ": [""]},"fail": ["a@a.com", "b@b.com"],"sms": {"succ": [], "fail": [ "1581234656", "13243458658" ]}, "snap": "path_1" },"level": "ok","ret": "str|file","title": "HTTP SVR OK","summary": "2/2 http check succeed","detail":[{"color": "", "title": "标题1","val": "intel"}],"auto" : [{"color": "", "title": "标题1","val": "intel"}],"extra": ""}}';

// }}}
// {{{ plugin

$plugin = '{
	"type": "plugin",
		"hid"    : "614b1c188295b488c030163f1d27d6e7",
		"parter_id" : 15,
		"hname" : "front-1_sem.tsinghua.edu.cn",
		"data": [{
			"name"      : "http_svr",
			"comment"   : "HTTP Service Check",
			"freq"      : "3min",
			"timeout"   : "2min",
			"errnum"    : "2",
			"snap"      : "none crit warn unkn succ tmout",
			"mail"      : "none crit warn unkn succ tmout",
			"sms"      : "none crit warn unkn succ tmout",
			"post"      : "none crit warn unkn succ tmout",
			"auto"      : "none crit warn unkn succ tmout",
			"attsnap"   : "0",
			"debug"     : "0",
			"mail_rec"  : "zhangguangzheng@eyou.net root_bbk@126.com",
			"sms_rec"  : "zhangguangzheng@eyou.net root_bbk@126.com",
			"handler"   : "default_handler",
			"udef"      : [ { "flag": "addr_port", "title": "端口", "val": "http:127.0.0.1:80 https:127.0.0.1:443"}]
		}
	]
}';

// }}}
// {{{ config

$config = '
{
    "type"  : "config",
    "hid"   : "614b1c188295b488c030163f1d27d6e7",
    "hname" : "front-1_sem.tsinghua.edu.cn",
	"parter_id" : 15,
    "data": {
        "global": {
            "scan_interval"         : 5,       
            "attach_ini_mail"       : 1,       
            "sysload_uplimit"       : 30,      
            "max_kidsnum"           : 50,      
            "plugin_maxlen"         : 65536,   
            "handler_maxlen"        : 32768,   
            "notify_onmisform"      : 1       
        },
        "default": {
            "comment"   : "Eminfo Plugin",
            "freq"      : "3min",
            "timeout"   : "2min",
            "errnum"    : "2",
            "snap"      : "none crit warn unkn succ tmout",
            "mail"      : "none crit warn unkn succ tmout",
            "post"      : "none crit warn unkn succ tmout",
            "auto"      : "none crit warn unkn succ tmout",
            "attsnap"   : "0",
            "debug"     : "0",
            "recevier"  : "zhangguangzheng@eyou.net root_bbk@126.com",
            "handler"   : "default_handler"
        },
        "sendmail": {
            "smtp_server"       : "smtp.sina.com.cn",
            "smtp_server_port"  : "25",
            "auth_user"         : "eyou_uetest@sina.com.cn",
            "auth_pass"         : "eyou-uetest",
            "timeout"          : "10",
            "charset"           : "utf8"
 
        },
        "postlog": {
            "post_server"       : "ota.eyou.net",
            "post_server_port"  : "1218",
            "charset"           : "utf-8",
            "post_timeout"     : "10",
            "post_max_length"   : "50000",
            "post_debug"        : 1
        },
        "clear_overdue": {
            "tmpfile_reserve_time"  : 7,    
            "logfile_reserve_time"  : 180,       
            "snapfile_reserve_time" : 7,      
            "snapdir_maxsize"       : 4096,
            "freq"                  : "3min" 
        },
        "log_rotate": {
            "force_size_uplimit": 1024,
            "freq"              : "3min"
        },
        "self_check": {
            "freq": "3min"
        },
        "iam_alive": {
            "freq": "3min"
        },
        "check_remote": {
            "freq": "3min"
        }
    }
}

';

// }}}



$redis = new Redis();

$redis->connect('127.0.0.1');

while(1) {
$arr = json_decode($config, true);
$config = json_encode($arr);
$redis->lpush('ma.json', $config);
}
