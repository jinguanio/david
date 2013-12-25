#!/bin/bash
#===============================================================================
#
#          FILE:  lcp.sh
# 
#         USAGE:  ./lcp.sh 
# 
#   DESCRIPTION:  语言项目程序发布
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  01/19/2011 02:07:43 PM CST
#===============================================================================

SOUR_PATH="\/data\/home\/libo\/my\/code\/parrot\/src\/web"
SOUR_PATH_1="\/home\/libo\/link\/parrot\/src\/web"
DIRE_PATH="/usr/local/eyou/devmail/web/lang"

CURR_PATH=`pwd`

if [ -z $1 ]; then
    echo "no parameters."
    exit 1
fi

params=${CURR_PATH/$SOUR_PATH/$DIRE_PATH}
if [ $params == $CURR_PATH ]; then
    params=${CURR_PATH/$SOUR_PATH_1/$DIRE_PATH}
fi

if [ ! -d $params ]; then
    sudo mkdir -p $params
fi

sudo cp $1 $params
echo "Command: sudo cp $1 $params"
