#!/bin/bash
#===============================================================================
#
#          FILE:  testunit.sh
# 
#         USAGE:  ./testunit.sh 
# 
#   DESCRIPTION:  php代码单元测试
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  04/13/2009 09:51:21 AM CST
#===============================================================================

#-------------------------------------------------------------------------------
#   参数设置
#-------------------------------------------------------------------------------
CMD_PARSE="/usr/local/eyou/devmail/app/bin/analysis_phpunit_log"
CMD_PHPUNIT="/usr/local/eyou/mail/opt/bin/phpunit"
CMD_MAIL="/home/libo/tool/php/send_mail.php"

LOG_PATH="/home/libo/log/phpunit"
LOG_FILE="$LOG_PATH/log_json.log"
LOG_TMP="$LOG_PATH/log_tmp.log"
PHP_TMP="$LOG_PATH/php_tmp.log"

TEST_PATH="/usr/local/eyou/mail/test/php/app/lib"
TEST_FILES="$( find $TEST_PATH -name 'testall_app_lib*.php' )"

# 文件模板
PHP_PATTERN="<?php define('EYOUM_TESTALL', true); "

# 缺省管理员邮件设置
ADMIN_EMAIL="libo@eyou.net"

#-------------------------------------------------------------------------------
#   目录环境确认
#-------------------------------------------------------------------------------
if [ ! -d "$LOG_PATH" ]; then
    mkdir -p "$LOG_PATH"
fi

#-------------------------------------------------------------------------------
#   产生日志
#-------------------------------------------------------------------------------
# 删除日志记录文件
if [ -e "$LOG_FILE" ]; then
    rm -f $LOG_FILE
fi

# create json-log file
declare -a php_fatal_files
php_fatal_num=0
for file in $TEST_FILES; do
    # 屏蔽指定文件的测试
    if [ "$TEST_PATH/testall_app_lib.php" = "$file" ]; then
        continue
    fi

    # 建立php临时文件
    content="$(cat $file)"
    echo "${content/<?php/$PHP_PATTERN}" > $PHP_TMP

    sudo $CMD_PHPUNIT --log-json $LOG_TMP $PHP_TMP &>/dev/null
    
    # 确定php执行错误
    if [ "$?" -eq "255" ]; then
        (( php_fatal_num += 1 ))
        php_fatal_files["$php_fatal_num"]="$file"
    fi
    
    # phpunit语句执行成功
    if [ -e "$LOG_TMP" ]; then
        cat $LOG_TMP >> $LOG_FILE
    else
        let "php_fatal_num += 1"
        php_fatal_files["$php_fatal_num"]="$file"
    fi

    # 删除php临时文件
    if [ -e "$PHP_TMP" ]; then
        rm -f $PHP_TMP
    fi

    # 删除log临时文件
    if [ -e "$LOG_TMP" ]; then
        rm -f $LOG_TMP
    fi
done

#-------------------------------------------------------------------------------
#   日志分析处理
#-------------------------------------------------------------------------------
$CMD_PARSE -f $LOG_FILE --sendmail 2>/dev/null

# 输出php fatal信息
if [ "$php_fatal_num" -ne "0" ]; then
    # 收件人设置
    if [ $# -ne 0 ]; then
        mail_to="$1"
    else
        mail_to="$ADMIN_EMAIL"
    fi

    # 拼装邮件体
    tmp_email="$LOG_PATH/email.txt"
    echo "Caution, produce fatal wrongs($php_fatal_num)." > "$tmp_email"
    for fatal_file in ${php_fatal_files[@]}; do
        echo "file: <$fatal_file>." >> "$tmp_email"
    done

    # 发送异常错误邮件
    $CMD_MAIL -from "phpunit-report" -to $mail_to -subject "PHP Fatal Wrongs" -body $tmp_email
    if [ "$?" -eq 0 -a -e "$tmp_email" ]; then
        rm -f "$tmp_email"
    fi
fi
