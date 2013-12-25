#!/bin/bash
#===============================================================================
#
#          FILE:  create_js_lang.sh
# 
#         USAGE:  ./create_js_lang.sh 
# 
#   DESCRIPTION:  建立项目中的 js 语言文件脚本
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  09/27/2010 10:15:52 AM CST
#===============================================================================

#-------------------------------------------------------------------------------
#  可配置参数 
#-------------------------------------------------------------------------------
project="guo_jia_tu_shu_guan"
compact="gjtshg"

#-------------------------------------------------------------------------------
#  定义常量 -- 一般不需改变
#-------------------------------------------------------------------------------
repo_path="/home/libo/my/code"
base_path="$repo_path/elephant_project"

parse_shell="$repo_path/elephant_tools/standard/src/shell"
in_po="$base_path/$project/plugins/inc/locale/zh_CN/LC_MESSAGES"
out_js="$base_path/$project/plugins/template/admin/tpl1/js"

#-------------------------------------------------------------------------------
#  压缩
#-------------------------------------------------------------------------------
$parse_shell/create_js -r < "$in_po/${compact}_admin_js.po" > "$out_js/${compact}_lang_zh.js"

sed -e "/var /s/Elang/${compact}Lang/g" "$out_js/${compact}_lang_zh.js" > $out_js/tmp
cat $out_js/tmp > "$out_js/${compact}_lang_zh.js"
rm $out_js/tmp

cd "$out_js"
sudo make "${compact}_lang_zh.js". >/dev/null

echo -e "rebuild <${compact}_lang_zh.js> file successfully!"

