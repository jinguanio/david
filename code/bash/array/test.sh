#!/bin/bash
#===============================================================================
#
#          FILE:  test.sh
# 
#         USAGE:  ./test.sh 
# 
#   DESCRIPTION:  
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2013年12月03日 15时50分03秒 CST
#===============================================================================

ARR=(
    pecl_memcached "memcached-1.0.0.tgz" "memcached-1.0.0"
    pecl_fileinfo  "Fileinfo-1.0.4.tgz" "Fileinfo-1.0.4"
    pecl_xdebug    "xdebug-2.0.5.tgz" "xdebug-2.0.5"
    pecl_dio       "dio-0.0.2.tgz" "dio-0.0.2"
    pecl_apc       "APC-3.0.19.tgz" "APC-3.0.19"
    pecl_imagick   "imagick-2.3.0.tgz" "imagick-2.3.0"
    pecl_gearman   "gearman-0.7.0.tgz" "gearman-0.7.0"
)

echo ${ARR[@]}
exit;
for i in "${ARR[@]}"; do
    echo $i
done
