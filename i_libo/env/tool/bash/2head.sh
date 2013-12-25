#!/bin/bash
#===============================================================================
#
#          FILE:  xhead.sh
# 
#         USAGE:  ./xhead.sh 
# 
#   DESCRIPTION:  显示前10行代码
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/15/2009 08:25:36 PM CST
#===============================================================================

if [ -z "$1" ]; then
    echo "fatal, routine has been stopped by xterm."
    exit 1
fi

head -n 20 $1
