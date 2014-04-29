<?php

// {{{ xml
$test = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!--  vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: -->
<monalert>

<hostgroups>
    <hostgroup>
        <name>hg_eyou_wan</name>
        <display_name>eYou 外网</display_name>
        <desc>eYou 外网</desc>
        <members>
            <member>lan-100.114</member>
        </members>
    </hostgroup>
</hostgroups>

<servicetpls>
    <servicetpl>
        <name>tpl_pop3</name>
    </servicetpl>
</servicetpls>

<hosts>
    <host>
        <name>eyou_wan-mail.eyou.net-switch</name>
        <display_name>mail.eyou.net 交换机</display_name>
        <desc>eYou 工作邮箱</desc>
        <!-- 主机的类型. server: 标准服务器, switch: 标准交换机. -->
        <type>switch</type>
    </host>

    <host>
        <!-- 继承的模板 -->
        <extends_tpls>
            <extends_tpl>mail_v504</extends_tpl>
        </extends_tpls>
        <name>lan-100.114</name>
        <display_name>mail.eyou.net</display_name>
        <desc>eYou 工作邮箱</desc>
        <!-- 主机的类型. server: 标准服务器, switch: 标准交换机. -->
        <type>server</type>
        <!-- 父主机, 例如某个交换机, 如果不设置则默认是一个交换机. -->
        <parent_host>eyou_wan-mail.eyou.net-switch</parent_host>
        <!-- 所有服务需要检测的时间段的强制值. 由 period 系列配置文件定义. -->
        <check_period_force>7x24</check_period_force>
        <!-- 所有服务排除检测的时间段的强制值. 例如服务器维护期间. 由 period 系列配置文件定义. -->
        <!-- check_exclude_period_force>temp_down</check_exclude_period_force -->
        <!-- 所有服务是否启用检测的强制值. 如果为 yes, 则所有服务都检测, 如果为 no, 所有服务都关闭检测. -->
        <!-- check_enabled_force>yes</check_enabled_force -->
        <!-- 所有服务是否启用通知的强制值. 如果为 yes, 则所有服务都通知, 如果为 no, 所有服务都关闭通知. -->
        <!-- notify_enabled_force>yes</notify_enabled_force -->
        <!-- 第一次检测的延迟时间, 防止刚启动检测进程的时候, 监控数据还没有送过来的情况. 单位: 秒. -->
        <first_check_delay>1</first_check_delay>
        <contacts>
            <contact>support_a</contact>
        </contacts>
        <contacts_groups>
            <contact_group>g_support</contact_group>
        </contacts_groups>

        <servicetpls>
            <servicetpl>
                <name>tpl_period</name>
                <display_name>pop3 服务</display_name>
                <desc>pop3 服务, 端口 110.</desc>
                <!-- 服务的类型. pop3/smtp/imap/http 等. -->
                <type>pop3</type>
                <!-- 初始化状态, o: up, d: down. -->
                <initial_state>o</initial_state>

                <!-- 需要检测的时间段. 由 period 系列配置文件定义. -->
                <check_period>7x24</check_period>
                <!-- 排除检测的时间段. 例如服务器维护期间. 由 period 系列配置文件定义. -->
                <check_exclude_period>temp_down</check_exclude_period>
                <!-- 是否启用检测 -->
                <check_enabled>yes</check_enabled>
                <!-- 检测的时间间隔, 单位: 秒. -->
                <check_interval>1</check_interval>
                <!-- 检测重试次数 -->
                <check_attempts>3</check_attempts>
                <!-- 检测方法 -->
                <check_method>
                    <name>metric</name>
                    <params>
                        <metric>mailproc_vsz_sum__em_pop3d</metric>
                        <!-- 阀值和检测值的对比关系, +: 大于阀值告警, -: 小于阀值告警, =: 和+/- 配合实现大于等于或小于等于 -->
                        <compare>+=</compare>
                        <attempts>3</attempts>
                        <warning_threshold>20</warning_threshold>
                        <crit_threshold>30</crit_threshold>
                    </params>
                </check_method>

                <!-- 是否启用通知 -->
                <notify_enabled>yes</notify_enabled>
                <!-- 需要通知的时间段. 由 period 系列配置文件定义. -->
                <notify_period>7x24</notify_period>
                <!-- 排除通知的时间段. 例如服务器维护期间. 由 period 系列配置文件定义. -->
                <notify_exclude_period>temp_down</notify_exclude_period>
                <!-- 通知的时间间隔, 单位: 秒. -->
                <notify_interval>300</notify_interval>
            </servicetpl>
        </servicetpls>

        <services>
			SERVICE_CONFIG
        </services>
    </host>
</hosts>

</monalert>
EOF;
// }}}

$service = <<<SER
            <service>
                <!-- 继承的本地 host 中的模板 -->
                <extends_tpls>
                    <extends_tpl scope="global">tpl_pop3</extends_tpl>
                    <extends_tpl scope="local">tpl_period</extends_tpl>
                </extends_tpls>
                <name>NAME</name>
                <check_interval>INTERVAL</check_interval>
            </service>
SER;

$services = '';
for ($i = 0; $i< 500; $i++) {
	$name = 'testpop_' . $i;
	$interval = mt_rand(15, 20);
	$xml_service = str_replace(array('NAME', 'INTERVAL'), array($name, $interval), $service);
	$services .= $xml_service;
}

$test = str_replace('SERVICE_CONFIG', $services, $test);

file_put_contents('a.xml', $test);
