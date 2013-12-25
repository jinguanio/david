#!/bin/bash
# 重构在线文档，并打开此次重构临时日志。
# 脚本不会向/usr/local/eyou/devmail/log/create_docs.log写入日志
# 目的是检查在线文档生成是否正确

DEST="/home/libo/tool"
BASH="$DEST/bash"

my() {
    if ! test 0 = $?; then
        echo "------- Throw Error ------"
        exit $?
    fi
}

# 更新svn
echo -n "whether or not updating svn [y/n]: "
read -n 1 make_sure
echo
if [ "$make_sure" == "y" ]; then
    $BASH/rebuild_svn.sh
fi

list="all php js db exit"
PS3="your choice: "
echo "select operating mode"
select item in $list; do
    case $item in
        "all")
            echo "Rebuild entire online document. Waiting..."
            sudo /usr/local/eyou/devmail/app/bin/create_docs 
            my
            echo "Ok."
            break
            ;;

        "php")
            echo "Rebuild online document of php section. Waiting..."
            sudo /usr/local/eyou/devmail/app/bin/create_phpdoc
            my
            echo "Ok."
            break
            ;;

        "js")
            echo "Rebuild online document of js section. Waiting..."
            sudo /usr/local/eyou/devmail/app/bin/create_jsdoc 
            my
            echo "Ok."
            break
            ;;

        "db")
            echo "Rebuild online document of db section. Waiting..."
            sudo /usr/local/eyou/devmail/app/bin/create_dbdoc 
            my
            echo "Ok."
            break
            ;;

        "exit")
            echo "Exiting."
            exit 0
            ;;

        *)
            echo "Haven't this parameters."
            exit 1
            ;;
        
    esac
done
