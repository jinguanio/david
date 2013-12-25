#!/bin/bash
# 批量测试指定的文件，可选的模式是xunit或者xhtml。
# 模式xhtml，要生成代码覆盖率报告。
# 模式xunit，只进行代码单元测试。

# phpunit 路径
path="/usr/local/eyou/mail/opt/bin"
xunit="$path/phpunit --colors"
xhtml="$path/phpunit --colors --coverage-html /usr/local/eyou/devmail/web/libo"
xphp="$path/php"

#
# 逻辑执行
#

echo -n "please input directory: "
read dir
#dir="/home/libo/crane/trunk/src/lib/member/operator/condition/test"

echo -n "please input file pattern (Don't use the wildcard): "
read patt
#patt="test_em_condition_adapter_member_operator_user_contacts*.php"

echo -n "please input the mode of php-unit [xunit(u)|xhtml(h)|xphp(p)]: "
read php_unit
case "$php_unit" in
    "xunit" | "u")
        command="$xunit"
        ;;
    "xhtml" | "h")
        command="$xhtml"
        ;;
    "xphp" | "p")
        command="$xphp"
        ;;
    *)
        echo "please retry to select."
        echo "exit."
        exit 1
esac
echo "you select $command mode."

list=$( ls -l "$dir" | grep -e "^-" | awk '{ print $9 }' | grep "$patt" )
num=0
for file in $list; do
    num=$(( ++num ))
    # 重复运行测试用例
    while true; do
        echo "current file: <$file>"
        sudo $command "$dir/$file"
        echo -en "\nwhether or not to execute once again [y/n]: "
        read -n 1 once_again
        if [ "$once_again" != "y" ]; then
            break
        fi
        echo
    done
    echo
done
echo -e "\ntotal: $num."
