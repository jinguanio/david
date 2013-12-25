#!/bin/bash
#===============================================================================
#
#          FILE:  build_jquery.sh
# 
#         USAGE:  ./build_jquery.sh 
# 
#   DESCRIPTION:  重建em_jquery.js文件
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  06/10/2009 02:02:13 PM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   参数设置
#-------------------------------------------------------------------------------
JQUERY_SRC="/home/libo/crane/trunk/src/web/tpl/admin/public/jquery/src"
JQUERY_PUBLISH="/home/libo/crane/trunk/src/web/tpl/admin/public/jquery"

#-------------------------------------------------------------------------------
#   函数
#-------------------------------------------------------------------------------
exception() {
    if ! test 0 = $?; then
        echo "------- Throw Error ------"
            exit $?
            fi
}

#-------------------------------------------------------------------------------
#   逻辑
#-------------------------------------------------------------------------------
cd "$JQUERY_SRC"
echo "create em_jquery_admin.js file, please wait..."
./build_jquery_js.sh
exception
echo "Ok, create em_jquery_admin.js file successfully."
cp em_jquery_admin.js ../

cd "$JQUERY_PUBLISH"
sudo make em_jquery_admin.js. >/dev/null
exception
echo "Ok, make operation successfully."
