<?php
error_reporting(E_ALL);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

// {{{ plugin_desc map

$map = 
array (
  0 => 
  array (
    'name' => 'imap_svr',
    'describe' => '检查IMAP服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查IMAP服务，通过模拟IMAP登录来测试IMAP服务状态和响应速度',
        'usage' => '<p>此插件共有四个参数：</p>
<p>addr_port imap地址和imap端口， 格式: 地址1:端口1 地址2:端口2</p>
<p>time_out 连接imap地址超时时间</p>
<p>imap_user 测试邮箱账户</p>
<p>imap_pass 测试邮箱密码</p>
<p>可以通过命令行工具eminfo config 手配置以上4个参数</p>
',
        'demo' => '#!/bin/bash
# 请以root账号执行以下命令
eminfo u imap_svr addr_port mail.eyou.net:143
eminfo u imap_svr time_out 10
eminfo u imap_svr imap_user zhangguangzheng@eyou.net
eminfo u imap_svr imap_pass password!@#
#配置完毕后可以测试运行：
eminfo run imap_svr',
      ),
    ),
  ),
  1 => 
  array (
    'name' => 'cpu_usage',
    'describe' => '检查CPU使用率情况',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查CPU使用率情况',
        'usage' => '此插件共有四个参数：
	 uplimit  CPU整体使用率(1-idle%)阈值上限
	 wa_uplimit    CPU等待占用率阀值上限
	 sy_uplimit CPU系统态占用率阀值上限
	 us_uplimit CPU用户态占用率阈值上限
可以通过命令行工具eminfo config 手工配置以上4个参数',
        'demo' => '# eminfo u cpu_usage uplimit 75
# eminfo u cpu_usage wa_uplimit 20
# eminfo u cpu_usage sy_uplimit 30
# eminfo u cpu_usage us_uplimit 30',
      ),
    ),
  ),
  2 => 
  array (
    'name' => 'disk_fs',
    'describe' => '检查磁盘各分区的文件系统状态和读写测试',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查磁盘各分区的文件系统状态和读写测试',
        'usage' => '此插件共有一个参数：
	 filesystem_fstype     只检查指定文件系统类型的磁盘分区
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u disk_fs filesystem_fstype "ext2 ext3 ext4"
# eminfo u disk_fs filesystem_fstype "ext2 ext3 ext4 nfs"
',
      ),
    ),
  ),
  3 => 
  array (
    'name' => 'disk_iostat',
    'describe' => '检查各磁盘设备的IO读写繁忙度',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查各磁盘设备的IO读写繁忙度',
        'usage' => '此插件共有两个参数：
	 dev_list 要检查的磁盘设备编号，如： /dev/sda1 /dev/sda2
	 util_uplimit IO繁忙度的阈值上限
可以通过命令行工具eminfo config 手工配置以上2个参数',
        'demo' => '# eminfo u disk_iostat dev_list  "/dev/sda1 /dev/sda2"
# eminfo u disk_iostat util_uplimit 50',
      ),
    ),
  ),
  4 => 
  array (
    'name' => 'disk_space',
    'describe' => '磁盘空间和磁盘节点使用量监控',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查磁盘空间使用率和磁盘节点使用率情况',
        'usage' => '此插件共有五个参数：
	 filesystem_fstype  只检查指定文件系统类型的磁盘分区
	 exclude_mpoint  跳过检查的挂载点
	 disk_spare_space_uplimit 每个分区的剩余磁盘空间告警阀值(单位M)
	 disk_spare_percent_uplimit 每个分区的剩余空间百分比告警阀值(单位百分比)
	 inode_spare_percent_uplimit 每个分区的剩余节点百分比告警阀值(单位百分比)
可以通过命令行工具eminfo config 手工配置以上5个参数',
        'demo' => '# eminfo u disk_space filesystem_fstype "ext2 ext3 ext4"
# eminfo u disk_space exclude_mpoint  "/boot /tmp"
# eminfo u disk_space disk_spare_space_uplimit 10000
# eminfo u disk_space disk_spare_percent_uplimit 10
# eminfo u disk_space inode_spare_percent_uplimit 10',
      ),
    ),
  ),
  5 => 
  array (
    'name' => 'dns_svr',
    'describe' => '检查DNS解析服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查DNS解析服务，通过模拟DNS解析来测试DNS解析结果和解析速度',
        'usage' => '此插件共有两个参数：
	 dns_check_lst  要测试的DNS解析对象和类型.    格式:  对象1:类型1,类型2,类型3   对象2:类型1,类型2
	 max_elapsed_time 解析每组DNS对象的最大超时时间(单位秒)
可以通过命令行工具eminfo config 手工配置以上2个参数',
        'demo' => '# eminfo u dns_svr dns_check_lst "eyou.net:mx,ns,soa  mail.eyou.net:a  8.8.8.8:ptr"
# eminfo u dns_svr max_elapsed_time 10',
      ),
    ),
  ),
  6 => 
  array (
    'name' => 'fdnum',
    'describe' => '文件打开数句柄监控',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查操作系统打开的所有文件句柄数量',
        'usage' => '此插件共有一个参数：
max_limit  系统打开的最大文件句柄数量阀值上限
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u fdnum max_limit 7500',
      ),
    ),
  ),
  7 => 
  array (
    'name' => 'http_svr',
    'describe' => '检查HTTP(s)服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查HTTP(s)服务，通过模拟HTTP(s) GET请求来测试HTTP(s)服务状态和响应速度',
        'usage' => '此插件共有两个参数：
	 addr_port http(s)地址和http(s)端口，    格式:  类型:地址1:端口1    类型:地址2:端口2
	 time_out  连接每个HTTP(s)地址的超时时间(单位秒)
可以通过命令行工具eminfo config 手工配置以上2个参数
',
        'demo' => '# eminfo u http_svr addr_port "http:127.0.0.1:80  https:mail.eyou.net:443"
# eminfo u http_svr time_out 10',
      ),
    ),
  ),
  8 => 
  array (
    'name' => 'mail_queue',
    'describe' => '检查eYouMail5/8邮件系统队列',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查eYouMail5/8邮件系统队列目录下堆积的信件数量',
        'usage' => '此插件共有一个参数：
	 uplimit 队列堆积信件数阀值上限
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u mail_queue uplimit 300',
      ),
    ),
  ),
  9 => 
  array (
    'name' => 'memcache_perf',
    'describe' => '检查Memcache服务的性能数据',
    'category' => '中间件',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查Memcache服务的性能数据，通过Memcache的stats命令获取Memcache的状态和性能信息',
        'usage' => '此插件共有四个参数：
	 addr_port  memcache地址和memcache端口，    格式:  地址1:端口1   地址2:端口2
	 time_out  连接每个memcache地址的超时时间(单位秒)
可以通过命令行工具eminfo config 手工配置以上2个参数
',
        'demo' => '# eminfo u memcache_perf addr_port "127.0.0.1:11211  127.0.0.1:11231  127.0.0.1:11251"
# eminfo u memcache_perf time_out 10',
      ),
    ),
  ),
  10 => 
  array (
    'name' => 'memory',
    'describe' => '检查系统内存和交换内存使用百分比情况',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '插件用于检查系统内存和交换内存使用百分比情况',
        'usage' => '此插件共有两个参数：
	 mem_uplimit 内存占用百分比阀值上限
	 swp_uplimit   交换占用百分比阀值上限 
可以通过命令行工具eminfo config 手工配置以上2个参数
',
        'demo' => '# eminfo u memory mem_uplimit 99.99
# eminfo u memory swp_uplimit 30',
      ),
    ),
  ),
  11 => 
  array (
    'name' => 'mysql_dump',
    'describe' => 'Mysql的DUMP备份',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于Mysql的DUMP备份',
        'usage' => '此插件共有五个参数：
	 mysqldump_path  mysqldump命令行程序的绝对路径
	 mysql_conn_conf  mysql连接方式的配置  格式: 地址,端口,帐号,密码
	 mysql_dump_tables  指定要备份的数据库和表的文件目标文件
	 dump_savedir   备份文件的导出和存放目录绝对路径
	 dump_reserve_time  备份文件的保存时间(单位天)
可以通过命令行工具eminfo config 手工配置以上5个参数',
        'demo' => '# eminfo u mysql_dump mysqldump_path "/usr/local/eyou/mail/opt/mysql/bin/mysqldump"
# eminfo u mysql_dump mysql_conn_conf "127.0.0.1,3306,eyou,eyou"
# eminfo u mysql_dump mysql_dump_tables "file:opt/mysql_dump.lst"
# eminfo u mysql_dump dump_savedir  "/data/mysql_dumpdir/"
# eminfo u mysql_dump dump_reserve_time 90',
      ),
    ),
  ),
  12 => 
  array (
    'name' => 'mysql_ping',
    'describe' => 'Mysql的Ping存活检测',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于Mysql的Ping存活检测',
        'usage' => '此插件共有三个参数：
mysqladmin_path  mysqladmin命令行程序的绝对路径
mysql_conn_conf  mysql连接方式的配置  格式: 地址,端口,帐号,密码
mysql_time_out  Mysql连接的超时时间(单位秒)
可以通过命令行工具eminfo config 手工配置以上3个参数',
        'demo' => '# eminfo u mysql_ping mysqladmin_path "/usr/local/eyou/mail/opt/mysql/bin/mysqladmin"
# eminfo u mysql_ping mysql_conn_conf "127.0.0.1,3306,eyou,eyou"
# eminfo u mysql_ping mysql_time_out 10',
      ),
    ),
  ),
  13 => 
  array (
    'name' => 'nfs_svr',
    'describe' => '检查NFS Server服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查NFS Server服务',
        'usage' => '此插件共有一个参数：
	 nfs_server_ip  NFS Server服务运行所在IP地址
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u nfs_svr nfs_server_ip 192.168.1.199',
      ),
    ),
  ),
  14 => 
  array (
    'name' => 'notify_oom',
    'describe' => '发现和提醒系统日志中出现的OOM事件',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于发现和提醒系统日志中出现的OOM事件',
        'usage' => '此插件共有一个参数：
	 messagefile  从哪个系统日志文件中抓取OOM事件
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u notify_oom messagefile "/var/log/messages"',
      ),
    ),
  ),
  15 => 
  array (
    'name' => 'notify_syslogin',
    'describe' => '发现和提醒系统用户登录',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于发现和提醒系统用户登录事件',
        'usage' => '此插件共有一个参数：
authfile  从哪个文件中抓取系统用户登录事件
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u notify_syslogin authfile "/var/log/secure"',
      ),
    ),
  ),
  16 => 
  array (
    'name' => 'pop_svr',
    'describe' => '检查POP服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查POP服务，通过模拟POP登录来测试POP服务状态和响应速度',
        'usage' => '此插件共有四个参数：
	 addr_port  pop地址和pop端口，    格式:  地址1:端口1   地址2:端口2
	 time_out  连接每个pop地址的超时时间(单位秒)
	 pop_user  测试邮箱账户
	 pop_pass  测试邮箱密码
可以通过命令行工具eminfo config 手工配置以上4个参数',
        'demo' => '# eminfo u pop_svr addr_port mail.yili.com:110
# eminfo u pop_svr time_out 10
# eminfo u pop_svr pop_user zhangguangzheng@yili.com
# eminfo u pop_svr pop_pass password!@#',
      ),
    ),
  ),
  17 => 
  array (
    'name' => 'port',
    'describe' => '检查端口的状态',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查端口的状态,通过连接测试检查端口能否正常接收外来连接',
        'usage' => '此插件共有一个参数：
	 port_list  要检查的地址和端口列表，    格式:  类型:地址1:端口1:连接超时时间1,  类型:地址2:端口2:连接超时时间2
	 默认值:  类型=tcp  地址=127.0.0.1  连接超时时间=5
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u port port_list "tcp:8.8.8.8:53:10, tcp:mail.eyou.net:443:10  22  192.168.1.1:110 "',
      ),
    ),
  ),
  18 => 
  array (
    'name' => 'process',
    'describe' => '检查正在运行进程的数量',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查正在运行进程的数量',
        'usage' => '此插件共有一个参数：
pslist_file  要被检查的进程列表文件
	 文件格式:
	  1. # 开头的行为注释行
	  2. 每行为一个要被检查的进程配置, 每行三个字段
	  3. 第一个字段为进程名(任意命名)
	  4. 第二个字段为进程最小运行个数
	  5. 第三个字段为进程匹配的正则表达式
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u process pslist_file "file:opt/process.lst"',
      ),
    ),
  ),
  19 => 
  array (
    'name' => 'remote_mailtest',
    'describe' => '测试当前服务器和其他邮件服务器的SMTP通讯情况',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于测试当前服务器和其他邮件服务器的SMTP通讯情况',
        'usage' => '此插件共有三个参数：
	 sample_email_file 要被测试的其他邮件服务器和测试帐号的列表文件
	 文件格式:
	 1. # 开头的行为注释行
	 2. 每行为一个要被测试的邮件服务器配置, 每行三个字段, 以 ::: 分隔
	 3. 第一个字段为对方邮件服务器的邮件域名
	 4. 第二个字段为对方邮件服务器的MX通信主机地址, 若留空则自动解析和探测
	 5. 第三个字段为对方邮件服务器上可接收信件的测试帐号列表, 多个邮箱列表空格分隔
	 mail_body_file SMTP发信测试的测试正文文件
helo_greeting_fqdn SMTP会话开始所使用的HELO值, 留空则使用 "eyou.net"可以通过命令行工具eminfo config 手工配置以上3个参数',
        'demo' => '# eminfo u remote_mailtest sample_email_file "file:opt/sample_email.dat"
# eminfo u remote_mailtest mail_body_file "file:opt/remote_mail.body"
# eminfo u remote_mailtest helo_greeting_fqdn "yili.com"',
      ),
    ),
  ),
  20 => 
  array (
    'name' => 'smtp_svr',
    'describe' => '检查SMTP服务',
    'category' => '服务',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查SMTP服务，通过模拟SMTP登录来测试SMTP服务状态和响应速度',
        'usage' => '此插件共有四个参数：
	 addr_port  smtp地址和smtp端口，    格式:  地址1:端口1   地址2:端口2
	 time_out  连接每个smtp地址的超时时间(单位秒)
	 smtp_user  测试邮箱账户
	 smtp_pass  测试邮箱密码
可以通过命令行工具eminfo config 手工配置以上4个参数',
        'demo' => '# eminfo u smtp_svr addr_port gateway.eyou.net:25
# eminfo u smtp_svr time_out 10
# eminfo u smtp_svr smtp_user zhangguangzheng@eyou.net
# eminfo u smtp_svr smtp_pass password!@#',
      ),
    ),
  ),
  21 => 
  array (
    'name' => 'sysload',
    'describe' => '检查系统负载值',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查系统负载值',
        'usage' => '此插件共有一个参数：
	 load_uplimit  系统负载阀值上限
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u sysload load_uplimit 10',
      ),
      'use_list' => 
      array (
        0 => '172.16.100.11<br />
        172.16.100.12<br />
        172.16.100.13<br />
        172.16.100.14',
      ),
    ),
  ),
  22 => 
  array (
    'name' => 'tcp_conn',
    'describe' => 'TCP连接数目监控',
    'category' => '系统',
    'os' => 'linux 5.8 +',
    'options' => 
    array (
      'describe' => 
      array (
        'feature' => '本插件用于检查本机TCP端口的连接数目',
        'usage' => '此插件共有一个参数：
port_list  要被检查的端口和连接数阀值上限
格式:  端口1:连接数1   端口2:连接数2
可以通过命令行工具eminfo config 手工配置以上1个参数',
        'demo' => '# eminfo u tcp_conn port_list "80:200  110:300  3306:120"',
      ),
    ),
  ),
);

// }}}

$db = em_db::singleton();
$table = 'plugin_desc';

foreach ($map as $r) {
    $bind['plugin_name'] = $r['name'];
    $bind['desc'] = $r['describe'];
    $bind['category'] = $r['category'];
    $bind['platform'] = $r['os'];
    $bind['source'] = 'ESOP';
    $bind['feature'] = $r['options']['describe']['feature'];
    $bind['usage'] = $r['options']['describe']['usage'];
    $bind['demo'] = $r['options']['describe']['demo'];
    //print_r($bind);
    //exit;

    $db->insert($table, $bind);
}

