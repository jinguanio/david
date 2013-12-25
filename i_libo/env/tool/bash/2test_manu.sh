#!/bin/bash
#===============================================================================
#
#          FILE:  xtestall.sh
# 
#         USAGE:  ./xtestall.sh 
# 
#   DESCRIPTION:  手动进行集成PHPUnit测试
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/16/2009 02:32:40 PM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   参数配置
#-------------------------------------------------------------------------------
# 本地路径
HOME="/home/libo"
LOG_TOOL_BASH="$HOME/tool/bash"
LOC_LOG_PATH="$HOME/log/phpunit"
LOC_LOG_FILE="$LOC_LOG_PATH/log_json.log"
LOC_LOG_TEMP="$LOC_LOG_PATH/log_tmp.log"
LOC_PHP_TEMP="$LOC_LOG_PATH/php_tmp.php"
LOG_PROCESS="$HOME/process.log"

# 目标路径
DEST_LIB_TEST_PATH="/usr/local/eyou/mail/test/php/app/lib"
DEST_TEST_FILES="$( find $DEST_LIB_TEST_PATH -name 'testall_app_lib*.php' )"

# 命令路径
CMD_UNIT_TEST="/usr/local/eyou/mail/opt/bin/phpunit"
CMD_LOG_CONVERT="/usr/local/eyou/devmail/app/bin/analysis_phpunit_log"

# 文件模板
PHP_PATTERN="<?php define('EYOUM_TESTALL', true); "

#-------------------------------------------------------------------------------
#   目录环境确认
#-------------------------------------------------------------------------------
# 目录不存在则创建目录
if [ ! -d "$LOC_LOG_PATH" ]; then
    mkdir -p "$LOC_LOG_PATH"
fi

#-------------------------------------------------------------------------------
#   异常处理
#-------------------------------------------------------------------------------
myexp() {
    if [ "$?" != 0 ]; then
        if [ -n "$1" ]; then
            echo "$1"
        else 
            echo "fatal, routine has been stopped by xterm."
        fi
        exit $?
    fi
}

#-------------------------------------------------------------------------------
#   清空处理过程日志
#-------------------------------------------------------------------------------
echo -n "whether to get process log [y/n]: "
read -n 1 get_process
if [ "$get_process" = "y" ]; then
    :>"$LOG_PROCESS"
fi
echo

#-------------------------------------------------------------------------------
#   更新目标文件和svn
#-------------------------------------------------------------------------------
echo -n "whether to update destination files or not [y/n]: "
read -n 1 upda
if [ "$upda" == "y" ]; then
    echo 
    $LOG_TOOL_BASH/xupdate.sh
    myexp
else 
    echo -e "\nOk, give up to update files."
fi

#-------------------------------------------------------------------------------
#   建立PHPUnit执行结果日志
#-------------------------------------------------------------------------------
# 删除日志记录文件
if [ -e "$LOC_LOG_FILE" ]; then
    rm -f $LOC_LOG_FILE
fi

# create json-log file
declare -a php_fatal_files
php_fatal_num=0
for file in $DEST_TEST_FILES; do
    # 屏蔽指定文件的测试
    if [ "$DEST_LIB_TEST_PATH/testall_app_lib.php" == "$file" ]; then
        continue
    fi

    # 建立php临时文件
    content="$(cat $file)"
    echo "${content/<?php/$PHP_PATTERN}" > $LOC_PHP_TEMP

    # 单元测试
    echo "Current file: <$file>"
    if [ "$get_process" = "y" ]; then
        echo "file: <$file>" >> $LOG_PROCESS
        sudo $CMD_UNIT_TEST --log-json $LOC_LOG_TEMP $LOC_PHP_TEMP >> $LOG_PROCESS
    else
        sudo $CMD_UNIT_TEST --colors --log-json $LOC_LOG_TEMP $LOC_PHP_TEMP 
    fi

    # 确定php执行错误
    if [ "$?" -eq "255" ]; then
        (( php_fatal_num += 1 ))
        php_fatal_files["$php_fatal_num"]="$file"
    fi

    # 判断文件是否存在
    if [ -e "$LOC_LOG_TEMP" ]; then 
        cat $LOC_LOG_TEMP >> $LOC_LOG_FILE
    else
        let "php_fatal_num += 1"
        php_fatal_files["$php_fatal_num"]="$file"
    fi

    # 删除php临时文件
    if [ -e "$LOC_PHP_TEMP" ]; then
        rm -f $LOC_PHP_TEMP
    fi

    # 删除log临时文件
    if [ -e "$LOC_LOG_TEMP" ]; then
        rm -f $LOC_LOG_TEMP
    fi
done

#-------------------------------------------------------------------------------
#   分析日志
#-------------------------------------------------------------------------------
echo -e "\n"
$CMD_LOG_CONVERT -f "$LOC_LOG_FILE" 2>/dev/null
myexp "Fatal, analysis log file failure."

# 输出php fatal信息
if [ "$php_fatal_num" -ne "0" ]; then
    echo -e "\033[41;37;1mCaution, produce fatal wrongs($php_fatal_num).\033[0m"
    for fatal_file in ${php_fatal_files[@]}; do
        echo -e "\033[41;37;1mfile: <$fatal_file>.\033[0m"
    done
fi
