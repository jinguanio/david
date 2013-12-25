#!/bin/bash
#===============================================================================
#
#          FILE:  xunit.sh
# 
#         USAGE:  ./xunit.sh 
# 
#   DESCRIPTION:  调用php-unit快捷方式
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/08/2009 02:48:41 PM CST
#===============================================================================

/usr/local/eyou/mail/opt/bin/phpunit --colors "$@"
