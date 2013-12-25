#!/bin/bash
# 清除/建立在线文档所需的Makefile文件 

# 路径设置
if [ -z "$1" ]; then
    echo -n "please input root directory (absolute-path): "
    read path
    if [ ! -d "$path/crane" ]; then
        echo "fatal, <$path/crane> do not exist."
        exit 1
    fi
else
    path="$1"
fi

# 参数设置
eyou_net="$path/crane/branches/eyou_net"
tools="$path/crane/trunk/tools"
src="$path/crane/trunk/src"

# 验证eyou_net参数
if [ ! -d "$eyou_net" ]; then
    echo "fatal, <$eyou_net> do not exists."
    exit 2
fi

# 验证tools参数
if [ ! -d "$tools" ]; then
    echo "fatal, <$tools> do not exists."
    exit 3
fi

# 验证src参数
if [ ! -d "$src" ]; then
    echo "fatal, <$src> do not exists."
    exit 4
fi

# 提示信息
usage () {
    echo -e "Usage: $0 {create|clean}.\n\n  create     Create Doc Makefile.\n  clean      Clean Doc Makefile."
}

# 错误处理
myexp() {
    if [ "$?" -ne "0" ]; then
        echo "fatal, routine has been stopped by xterm."
        exit "$?"
    fi
}

# 清除makefile文件
clean_makefile () {
    cd "$eyou_net"
    ./configure clean &> /dev/null
    myexp
    echo "OK, clean makefile successfully in '$eyou_net'."

    cd "$tools"
    ./configure clean &> /dev/null
    myexp
    echo "OK, clean makefile successfully in '$tools'."

    cd "$src"
    ./configure clean &> /dev/null
    myexp
    echo "OK, clean makefile successfully in '$src'."
}

# 建立makefile文件
create_makefile () {
    cd "$eyou_net"
    ./configure create &> /dev/null
    myexp
    echo "OK, create makefile successfully in '$eyou_net'."

    cd "$tools"
    ./configure create &> /dev/null
    myexp
    echo "OK, create makefile successfully in '$tools'."

    cd "$src"
    myexp
    ./configure create &> /dev/null
    echo "OK, create makefile successfully in '$src'."
}

if [ -z "$2" ]; then
    echo -n "please input operating mode [cl_ean/cr_eate]: "
    read mode
else
    mode="$2"
fi
case "$mode" in
    cl|clean)
        clean_makefile
        ;;
    cr|create)
        create_makefile
        ;;
    *)
        clean_makefile
        ;;
esac
