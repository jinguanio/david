#!/bin/bash
#===============================================================================
#
#          FILE:  update_env.sh
# 
#         USAGE:  ./update_env.sh 
# 
#   DESCRIPTION:  
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2013年10月11日 16时34分26秒 CST
#===============================================================================

PATH_STATIC="/usr/local/eyou/mail/web/apps/mailadmin/static"
PATH_TPL="/usr/local/eyou/mail/apps/mailadmin/tpl"
PATH_PHP="/usr/local/eyou/mail/apps/mailadmin/action/app"

PATH_GIT="/home/libo/git/new/src/apps/mailadmin"

cmd_help ()
{
    echo "$0 [all | js | eele | os | css | images | tpl | php]"
    exit 1
}

update_js () 
{
    sudo rm -rf $PATH_STATIC/js/*

    # lang
    cd /home/libo/my/git/elephant_dev/standard/src/shell
    ./em_build_lang -d app_mailadmin_js >/dev/null

    cd $PATH_GIT/tpl/js
    make_code js

}

update_eele () 
{
    sudo rm -rf $PATH_STATIC/eele/*

    cd $PATH_GIT/tpl/eele
    make_code eele

}

update_os () 
{
    sudo rm -rf $PATH_STATIC/os/*

    cd $PATH_GIT/tpl/os
    make_code os

}

update_css () 
{
    sudo rm -rf $PATH_STATIC/css/*
    cd $PATH_GIT/tpl/css
    make_code css
}

update_images () 
{
    sudo rm -rf $PATH_STATIC/images/*
    cd $PATH_GIT/tpl/images
    make_code images
}

update_tpl () 
{
    sudo rm -rf $PATH_TPL/*
    cd $PATH_GIT/tpl
    make_code tpl
}

update_php () 
{
    sudo rm -rf $PATH_PHP/*
    cd $PATH_GIT/action/app
    make_code php
}

make_code ()
{
    jjcm >/dev/null
    sudo make copy >/dev/null
    echo "+OK $1"
}

if [ -z $1 ]; then
    cmd_help
fi;

echo "update code starting..."
case "$1" in
    js)
    update_js
    ;;

    eele)
    update_eele
    ;;

    os)
    update_os
    ;;

    css)
    update_css
    ;;

    images)
    update_images
    ;;

    tpl)
    update_tpl
    ;;

    php)
    update_php
    ;;

    all)
    update_js
    update_eele
    update_os
    update_css
    update_images
    update_tpl
    update_php
    ;;

    *)
    cmd_help
    ;;
esac
