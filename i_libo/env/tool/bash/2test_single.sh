#!/bin/bash
# phpunit测试，xhtml会输出覆盖率，xunit只进行单元测试。

#
# 参数设置
#

MODE="xunit xhtml xphp quit"
PATH=`pwd`
PS3="your choice: "

#
# 逻辑
#

# phpunit 路径
path="/usr/local/eyou/mail/opt/bin"
xunit="$path/phpunit --colors"
xhtml="$path/phpunit --colors --coverage-html /usr/local/eyou/devmail/web/libo"
xphp="$path/php"

if [ -n "$1" ]; then
    FILE="$1"
    echo "the file of your choice: <$1>"
else
    echo -n "please input file name(current directory): "
    read file
    FILE="$file"
fi

if [ ! -e "$PATH/$FILE" ]; then
    echo "<$PATH/$FILE> is not file."
    exit 1
fi

select mode in $MODE; do
    case "$mode" in 
        "xunit")
            $xunit "$FILE"
            ;;
        "xhtml")
            $xhtml "$FILE"
            ;;
        "xphp")
            $xphp "$FILE"
            ;;
        *)
            echo "exit."
            break
            ;;
    esac
done
