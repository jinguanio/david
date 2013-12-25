#!/bin/bash
#===============================================================================
#
#          FILE:  publish.sh
# 
#         USAGE:  ./publish.sh 
# 
#   DESCRIPTION:  publish
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2012年08月01日 18时54分29秒 CST
#===============================================================================

#!/bin/bash

#开启路由功能

echo 1 >/proc/sys/net/ipv4/ip_forward

#清楚所有ipvsadm规则

ipvsadm -C

#添加映射表

ipvsadm -At 192.168.5.128:80 -s rr
#其中 -A表示添加  -s 选择轮询模式 rr表示轮叫模式

ipvsadm -at 192.168.5.128:80 -r 192.168.81.130:80 -m

ipvsadm -at 192.168.5.128:80 -r 192.168.81.131:80 -m

# 备注的其它操作
#
# GRANT ALL ON *.* TO root@'%' IDENTIFIED BY "aaaaa123"
