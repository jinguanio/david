#!/bin/bash
#===============================================================================
#
#          FILE:  mlang.sh
# 
#         USAGE:  ./mlang.sh 
# 
#   DESCRIPTION:  发布语言
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2012年01月06日 15时59分17秒 CST
#===============================================================================

cd /home/libo/my/git/elephant_dev/standard/src/shell

if [ "$1" == 'php' ]; then
    ./em_build_lang -d app_mailadmin -r 1
elif [ "$1" == 'js' ]; then
    ./em_build_lang -d app_mailadmin_js
else
    ./em_build_lang -d app_mailadmin -r 1
    echo "=========================================="
    ./em_build_lang -d app_mailadmin_js
fi

