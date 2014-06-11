#!/bin/bash
#===============================================================================
#
#          FILE:  vopen.sh
# 
#         USAGE:  ./vopen.sh 
# 
#   DESCRIPTION:  打开 web lib 和 api 对应文件
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2014年06月11日 11时17分04秒 CST
#===============================================================================

if [ -z $@ ]; then
    echo "./`basename $0` [suffix]"
    exit 1;
fi

git="/home/libo/git"
api="$git/tk/src/lib/api/action"
api_prefix="em_httpapi_action_api_"
api_file="$api/$api_prefix$1.class.php"

weblib="$git/eagleeye/src/app/lib/monitor/operator"
weblib_prefix="em_monitor_property_operator_"
weblib_file="$weblib/$weblib_prefix$1.class.php"

vim -O $api_file $weblib_file
