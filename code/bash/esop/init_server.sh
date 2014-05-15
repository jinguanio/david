#!/bin/bash
#===============================================================================
#
#          FILE:  init.sh
# 
#         USAGE:  ./init.sh 
# 
#   DESCRIPTION:  
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2014年03月26日 00时28分57秒 CST
#===============================================================================

git_branch="release/15710-v1.0.1"
path_base="/usr/local/eyou/toolmail"
path_git_tk="/home/libo/git/tk/src"
path_git_eagleeye="/home/libo/git/eagleeye/src"
path_nfs="/data/esop_servercodes"

# 停止 toolmail
sudo eyou_toolmail stop

# clear toolmail
sudo rm -fr $path_base
echo "start install...."
sleep 3

# push git code
cd $path_git_tk
git fetch
git pull origin $git_branch
./configure create
sudo make install

cd $path_git_eagleeye
git fetch
git pull origin $git_branch
./configure create
sudo make install

# 生成 opt 
sudo cp -a $path_base/../toolmail.bak/opt $path_base/

# 建立目录
sudo mkdir -p $path_base/{log,tmp,data,run}
sudo chown -R eyou:eyou $path_base/{log,run,tmp}

sudo mkdir -p $path_base/log/redis
sudo chown -R eyou:eyou $path_base/log/redis

sudo mkdir -p $path_base/tmp/php
sudo chown -R eyou:eyou $path_base/tmp/php

sudo mkdir -p $path_base/data/redis
sudo chown -R eyou:eyou $path_base/data/redis

sudo mkdir -p $path_base/app/mc/template/tpl_c
sudo chmod 777 $path_base/app/mc/template/tpl_c
sudo chown -R eyou:eyou $path_base/app/mc/template/tpl_c

# init mysql
cd $path_base/opt/mysql
sudo ./scripts/mysql_install_db --defaults-file=/usr/local/eyou/toolmail/etc/mysql/my.cnf --datadir=/usr/local/eyou/toolmail/data/mysql

# 启动 eyou_toolmail
sudo eyou_toolmail start mysql
$path_base/opt/mysql/bin/mysql -uroot -S /usr/local/eyou/toolmail/run/etm_mysql.sock < $path_git_tk/sql/db_creation_eyou_monitor.sql
$path_base/opt/mysql/bin/mysql -uroot -S /usr/local/eyou/toolmail/run/etm_mysql.sock < $path_git_tk/sql/db_init_eyou_monitor.sql

# 备份目标目录
sudo eyou_toolmail stop 
sudo rm -fr $path_nfs/*
sudo cp -a $path_base/{web,etc,app,implements,tmp_install} $path_nfs

# 启动 eyou_toolmail 服务
sudo eyou_toolmail start 
sudo eyou_toolmail watch

