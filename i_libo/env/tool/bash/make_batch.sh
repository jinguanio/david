#!/bin/bash
#===============================================================================
#
#          FILE:  make_batch.sh
# 
#         USAGE:  ./make_batch.sh 
# 
#   DESCRIPTION:  批量提交代码到目标环境
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/28/2009 03:15:07 PM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   获取文件目录
#-------------------------------------------------------------------------------
echo -n "please input the file's directory: "
read dir
if [ ! -d "$dir" ]; then
    echo "fatal, the directory do not exists."
    exit 1
fi

#-------------------------------------------------------------------------------
#   需要make目标的文件模板
#-------------------------------------------------------------------------------
echo -n "please input file pattern(command 'ls' may need wildcard): "
read patt

#-------------------------------------------------------------------------------
#   异常处理
#-------------------------------------------------------------------------------
myexp() {
    if [ "$?" -ne "0" ]; then
        echo "fatal, routine has been stopped by xterm."
        exit "$?"
    fi
}

#-------------------------------------------------------------------------------
#   make file
#-------------------------------------------------------------------------------
cd "$dir"
if echo "$dir" | grep -wq "tools"; then
    kkcm
    myexp
    echo "Ok, execute 'kkcm'command successfully."
else
    jjcm
    myexp
    echo "Ok, execute 'jjcm'command successfully."
fi

ls $patt | while read file; do
    if [ ! -e "$file" ]; then
        continue
    fi
    sudo make "$file." &>/dev/null
    myexp
    echo "Ok, make <$file> successfully."
done
