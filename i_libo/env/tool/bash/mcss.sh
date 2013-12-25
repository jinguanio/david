#!/bin/bash
#===============================================================================
#
#          FILE:  mcss.sh
# 
#         USAGE:  ./mcss.sh 
# 
#   DESCRIPTION:  make art css
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  2011年12月31日 14时42分37秒 CST
#===============================================================================

sudo cp /home/libo/my/code/elephant_artd/os/plugin/admin/css/mailadmin.css /usr/local/eyou/mail/web/tpl/plugins/mailadmin/app/tpl1/css/
echo "+Ok. Copy to destination."

cp /home/libo/my/code/elephant_artd/os/plugin/admin/css/mailadmin.css ~/my/code/elephant/src/plugins/mailadmin/template/app/tpl1/css/
echo "+Ok. Copy to git repository."

