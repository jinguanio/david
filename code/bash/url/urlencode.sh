#!/bin/bash
#===============================================================================
#
#          FILE:  t.sh
# 
#         USAGE:  ./t.sh 
# 
#   DESCRIPTION:  
# 
#        AUTHOR:  jacob, libo@eyou.net
#       CREATED:  08/08/2014 10:07:14 AM CST
#===============================================================================

urlencode() {
    # urlencode <string>
 
    local length="${#1}"
    for (( i = 0; i < length; i++ )); do
        local c="${1:i:1}"
        case $c in [a-zA-Z0-9.~_-]) 
            printf "$c" 
            ;;
        *) printf '%%%02X' "'$c"
        esac
    done
}
 
urldecode() {
    # urldecode <string>
 
    local url_encoded="${1//+/ }"
    printf '%b' "${url_encoded//%/\x}"
}

urldecode $(urlencode "a+b")
