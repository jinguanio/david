#!/bin/bash

##############################
#####安装环境脚本#############
##############################


TOOL_PATH=$PWD

mkdir /tmp/hzx/home1
for file in `ls -a`
do
    if  [ "$file" != '..' ] && [ "$file" != '.' ] && [ "$file" != 'setup.sh' ]; then
        if [ -d  "$HOME/$file"] || [ -f "$HOME/$file" ] ;then
            mv -f $HOME/$file /tmp/hzx/home1
        fi
        ln -s $TOOL_PATH/$file $HOME/$file
    fi
done
