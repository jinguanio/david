#!/bin/bash
#===============================================================================
#
#          FILE:  scp_sync_file.sh
# 
#         USAGE:  ./scp_sync_file.sh 
# 
#   DESCRIPTION:  同步指定目录指定文件
# 
#       OPTIONS:  ---
#  REQUIREMENTS:  ---
#          BUGS:  ---
#         NOTES:  ---
#        AUTHOR:   (), 
#       COMPANY:  
#       VERSION:  1.0
#       CREATED:  04/08/2009 11:25:05 AM CST
#      REVISION:  ---
#===============================================================================


#-------------------------------------------------------------------------------
#   参数设置
#-------------------------------------------------------------------------------
SERVER=("172.16.100.37" "172.16.100.15" "172.16.100.115" "172.16.100.60")

#===  FUNCTION  ================================================================
#          NAME:  myexp
#   DESCRIPTION:  异常处理
#    PARAMETERS:  
#       RETURNS:  
#===============================================================================
myexp() {
    if [ "$?" != "0" ]; then
        if [ -n "$1" ]; then
            echo "msg: $1"
        else
            echo "fatal, routine has been stopped by xterm."
        fi
        exit $?
    fi
}

#-------------------------------------------------------------------------------
#   读取要同步的文件
#-------------------------------------------------------------------------------
echo -n "please input synchronous filename (absolute-path): "
read tmp_file
if [ ! -e "$tmp_file" ]; then
    echo "msg: <$tmp_file> is not a valid file."
    exit 1
else
    FILE="$tmp_file"
fi

for serv in ${SERVER[@]}; do
    echo -n "synchronize <$serv> server [y/n]: "
    read -n 1 sync
    echo
    if [ "$sync" = "y" ]; then
        scp $FILE libo@$serv:$FILE
        myexp
        echo -e "Ok.\n"
    else
        echo -e "Skip!\n"
    fi
done

if [ -e "$FILE" ]; then
    echo -n "whether to delete synchronical file <$FILE> [y/n]: "
    read -n 1 is_dele
    if [ "$is_dele" = "y" ]; then
        rm -f $FILE
        echo -e "\nOk, delete file successfully."
    else
        echo -e "\nSkip!"
    fi
    echo
fi
