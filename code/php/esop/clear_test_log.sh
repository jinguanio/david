#!/bin/bash
#===============================================================================
#
#          FILE:  clear_test_log.sh
# 
#         USAGE:  ./clear_test_log.sh 
# 
#   DESCRIPTION:  
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2014年06月26日 09时43分27秒 CST
#===============================================================================

>/tmp/serv
>/tmp/cli
echo "clear /tmp log succ"

sudo cp /dev/null /usr/local/esop/agent/log/phptd.log
sudo cp /dev/null /usr/local/eyou/toolmail/log/phptd.log
echo "clear phptd log succ"

