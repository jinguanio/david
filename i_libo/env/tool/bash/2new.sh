#!/bin/bash
# 建立文件并设定访问权限

#
# 错误处理
#

myexp() {
    if [ "$?" != "0" ]; then
        if [ "$1" ]; then
            echo "$1"
        fi
        echo "fatal, program has stopped."
        exit $?
    fi
}

#
# 逻辑
#

if [ "$1" ]; then
    if [ -a "$1" ]; then
        echo -n "the file of <$1> has existed, whether or not to continue [y/n]: "
        read -n 1 go_on
        if [ "$go_on" = "y" ]; then
            touch "$1"
        fi
        echo
    else
        touch "$1"
        myexp "touch file has a error."
        if [ "$2" ]; then
            chmod "$2 $1"
        else
            chmod 755 "$1"
        fi
        myexp "the command of chmod has a error."
    fi
fi
