#!/bin/bash
#===============================================================================
#
#          FILE:  xpatch.sh
# 
#         USAGE:  ./xpatch.sh 
# 
#   DESCRIPTION:  获取 patch 文件
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  09/06/2010 04:09:40 PM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   配置
#-------------------------------------------------------------------------------
# git 根目录，存在 .git 的目录
GIT_ROOT="/home/libo/project"

# patch 目录 key (/tmp/xxxx)
PATCH_KEY="patch_1" 

# 起始 revision
START_REVISION="81767c580e82b5ac310d11aa03d4e8fe36ff874c"

# 终止 revision
END_REVISION="HEAD"

# 过滤文件（列表中的文件不会被处理）
#FILTER_FILES="em_admin_js.po em_user_js.po"
FILTER_FILES="hebgcdx_user_js.po"

#-------------------------------------------------------------------------------
#   预定义变量
#-------------------------------------------------------------------------------
ECHO_FAIL="[\033[0;31mFAIL\033[0m]"
ECHO_OK="[\033[0;32mOK\033[0m]"

# 补丁包中的安装脚本名称
PATCHER_NAME="make_install.sh"
PATCH_PATH="/tmp/$PATCH_KEY"

#-------------------------------------------------------------------------------
#   函数定义
#-------------------------------------------------------------------------------
_echo() {
    if [ "$?" != "0" ]; then
        echo -e "$ECHO_FAIL"
        exit $?
    else 
        echo -e "$ECHO_OK"
    fi
}

create_patch() {
    cd $GIT_ROOT

    revision_list=`git log --pretty=format:"%H" $START_REVISION..$END_REVISION`

    if [ -d $PATCH_PATH ]; then
        rm -rf $PATCH_PATH
    fi
    mkdir -p $PATCH_PATH

    echo -n "Create patch files    "
    for revision in $revision_list; do
        for file_name in `git show --name-only --pretty=oneline $revision | awk '{ print $1 }'`; do
            if [ $revision == $file_name ]; then
                continue
            fi

            path=`dirname $file_name`
            file=`basename $file_name`

            if echo $FILTER_FILES | grep -wq $file; then
                continue
            fi

            if [ -e "$PATCH_PATH/$path/$file" ]; then
                continue
            fi

            if [ ! -d "$PATCH_PATH/$path" ]; then
                mkdir -p "$PATCH_PATH/$path"
            fi

            cp "$path/$file" "$PATCH_PATH/$path" &>/dev/null
            if [ "$?" != "0" ]; then
                echo -e "\n\033[0;31mWarning, ["$path/$file"] is not deleted!\033[0m"
                continue
            fi

            if [ ! -e "$PATCH_PATH/$path/Makefile" ]; then
                cd $path
                jjcm >/dev/null
                cp Makefile "$PATCH_PATH/$path"
                cd $GIT_ROOT
            fi
        done
    done

    build_patcher
    _echo
}

build_patcher() {
    cd $PATCH_PATH

    echo "#!/bin/bash" > $PATCHER_NAME
    echo "PATCH_PATH=\"$PATCH_PATH\"" >> $PATCHER_NAME
    echo "PATCHER_NAME=\"$PATCHER_NAME\"" >> $PATCHER_NAME
    echo 'ECHO_FAIL="[\033[0;31mFAIL\033[0m]"' >> $PATCHER_NAME
    echo 'ECHO_OK="[\033[0;32mOK\033[0m]"' >> $PATCHER_NAME
    echo '
_echo() {
    if [ "$?" != "0" ]; then
        echo -e "$ECHO_FAIL"
        exit $?
    else 
        echo -e "$ECHO_OK"
    fi
}

for item in `find -type f`; do
    if echo $item | grep -wq "$PATCHER_NAME"; then
        continue
    fi

    if echo $item | grep -wq 'Makefile'; then
        continue
    fi

    echo -n "$item    "
    item_path=`dirname $item`
    item_file=`basename $item`

    cd $item_path
    make $item_file. >/dev/null
    _echo
    cd $PATCH_PATH
done' >> $PATCHER_NAME

    chmod 777 $PATCHER_NAME
}

install_patch() {
    cd $PATCH_PATH
    for item in `find -type f`; do
        if echo $item | grep -wq "$PATCHER_NAME"; then
            continue
        fi

        if echo $item | grep -wq 'Makefile'; then
            continue
        fi

        echo -n "$item    "
        item_path=`dirname $item`
        item_file=`basename $item`

        cd $item_path
        make $item_file. >/dev/null
        _echo
        cd $PATCH_PATH
    done
}

#-------------------------------------------------------------------------------
#  处理过程  
#-------------------------------------------------------------------------------
# 验证GIT_ROOT参数
if [ ! -d $GIT_ROOT/.git ]; then
    echo -e "\033[0;31mFatal, GIT_ROOT is wrong.\033[0m"
    exit 2
fi

echo -n "Whether or not to create patch files [y/n]: "
read is_create

if [ "y" == "$is_create" ]; then
    create_patch
    echo
fi

echo -n "Whether or not to install patch files [y/n]: "
read is_create

if [ "y" == "$is_create" ]; then
    install_patch
fi
