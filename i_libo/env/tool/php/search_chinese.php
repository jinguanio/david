#!/usr/local/eyou/devmail/opt/bin/php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 批量替换权限
 * 
 * @category   eYou_Mail
 * @package    Em_
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id:$
 */

/**
 * require
 */
require_once 'functions.php';


// {{{  参数定义

$chinese_pattern = '\x{4e00}-\x{9fa5}';
$escape_pattern = '\*|\/\/|<!--|\{\{\*';
$log = 'log/search_chinese.log'; 

// 替换路径
$paths = array(
            '/home/libo/git/src/web/tpl/admin/tpl1'         => '*.html',
            '/home/libo/git/src/web/tpl/admin/tpl1/js'      => '*.js',
            '/home/libo/src/web/php'                        => '*.php',
            '/home/libo/src/lib/acl'                        => '*.php',
        );

//$paths = array('/home/libo/test' => '*.txt');

// }}}

if (is_file($log)) {
    unlink($log);
}
$flog = fopen($log, 'a+');

foreach ($paths as $path => $file_pattern) {
    $files = rglob($file_pattern, 0, $path);

    foreach ($files as $file) {
        if (false !== strpos($file, 'lang_zh.js')) {
            continue;
        }

        $fp = @fopen($file, 'r');
        $line = $match_num = $count = 0;
        $buffer_line = array();

        if ($fp) {
            while (!feof($fp)) {
                $buffer = fgets($fp, 4096);
                $line++;

                // 匹配注释中有汉字
                if (0 !== preg_match("/^\s*[$escape_pattern]+\s*/", $buffer, $matches)) {
                    continue;
                }

                // 匹配 // xxxxxb 
                if (0 !== preg_match("/\/\/\s*[$chinese_pattern]+/u", $buffer, $matches)) {
                    continue;
                }

                $match_num = preg_match_all("/[$chinese_pattern]+/u", $buffer, $matches);
                if (0 !== $match_num) {
                    $buffer_line[] = $line;
                    $count += $match_num;
                }
            }
        }

        if (0 !== $count) {
            fwrite($flog, "Search file: <$file>\n");
            fwrite($flog, "    Count: $count \n");
            fwrite($flog, "    Line: " . implode(', ', $buffer_line) . " \n");
            fwrite($flog, "\n");
        }
    }
}

fclose($flog);

