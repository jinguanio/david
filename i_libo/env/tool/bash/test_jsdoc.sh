#!/bin/bash

# 生成 jsdoc
JAVA_BIN="/usr/java/jdk1.6.0_07/bin/java"
JSDOC_TOOLKIT="/usr/local/eyou/devmail/tools/jsdoc_toolkit"
JS_DIR="/usr/local/eyou/mail/web/tpl/user/tpl1/js"
OUT_DIR="/usr/local/eyou/devmail/web/docs/jsdoc"
JS_PATH="/home/libo/crane/branches/eyou_net/web/tpl/user/tpl1/js/bookmark.js"

#rm -rf $OUT_DIR/*
$JAVA_BIN -jar $JSDOC_TOOLKIT/jsrun.jar $JSDOC_TOOLKIT/app/run.js \
        $JS_PATH -Z            



          #-a \ 
          #-p \
          #-t=$JSDOC_TOOLKIT/templates/jsdoc \
          #-d=$OUT_DIR $JS_DIR \

