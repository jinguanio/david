#!/bin/bash
#===============================================================================
#
#          FILE:  search_group_list.sh
# 
#         USAGE:  ./search_group_list.sh 
# 
#   DESCRIPTION:  test
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  12/28/2009 02:20:42 PM CST
#===============================================================================

OWN="/home/libo"
COMMITS="/tmp/libo"
RESULT_PAHT="$OWN/result.txt"
GIT="$OWN/git"

SEARCH_LINES=20 
SEARCH_FILE="src/web/tpl/admin/tpl1/js/group_list.js"
SEARCH_CONTENT="this._getUserList="

# clear
:>$RESULT_PAHT

# search
cd $GIT
git log $SEARCH_FILE | grep "commit" | awk '{printf("%s\n", $2)}' > $COMMITS

for commit in `cat $COMMITS`; do
    echo -e "[commit: $commit]" 
    git show $commit:$SEARCH_FILE | grep -A $SEARCH_LINES "$SEARCH_CONTENT" 
    echo -e "\n=================================\n" 
done
