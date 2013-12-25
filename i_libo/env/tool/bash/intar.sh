#!/bin/bash
#===============================================================================
#
#          FILE:  install_tar.sh
# 
#         USAGE:  ./install_tar.sh 
# 
#   DESCRIPTION:  安装tar文件
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/17/2009 01:18:36 PM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   错误处理
#-------------------------------------------------------------------------------
myexp() {
    if [ "$?" != "0" ]; then
        if [ -n "$1" ]; then
            echo "$1"
        else
            echo "Fatal, routine has been stopped by xterm."
        fi
        exit $?
    fi
}

#-------------------------------------------------------------------------------
#   设置安装目录
#-------------------------------------------------------------------------------
echo -n "please input directory for installing: "
read direc
if [ ! -d "$direc" ]; then
    echo "Fatal, input directory error."
    exit 1
else
    echo "Ok, check directory successfully."
fi

#-------------------------------------------------------------------------------
#   设置安装文件
#-------------------------------------------------------------------------------
echo -n "please input .tar file for installing: "
read tarfile
if [ ! -e "$tarfile" ]; then
    echo "Fatal, .tar is not a regular file."
    exit 1
else
    echo "Ok, check .tar file successfully."
fi

#-------------------------------------------------------------------------------
#   安装tar包
#-------------------------------------------------------------------------------
rm -rf "$direc"
myexp "Fatal, delete destination directory failure."
echo "Ok, delete destination directory successfully."

tar -xf "$tarfile" -C "${direc%/*}"
myexp "Fatal, extract .tar file failure."
echo "Ok, extract .tar file successfully."

rm -f "$tarfile"
myexp "Fatal, delete .tar file failure."
echo "Ok, delete .tar file successfully."
