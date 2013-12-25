<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 查找没有@package标签的页面，返回文件名和路径。
 * 
 * @category   Tools
 * @package    Tools_style
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id:$
 */
require_once 'functions.php';

$res = '';
$files = rglob('*.class.php', 0, '/usr/local/eyou/mail/app/lib/php/');
foreach ($files as $file) {
    if (is_file($file)) {
        $contents = file_get_contents($file);
        $match = preg_match('/@package/i', $contents);
        if (0 === $match) {
            $res .= $file . "\n";
        }
    }
}

file_put_contents('/tmp/libo', $res);
if (!empty($res)) {
    echo $res . "\n";
}
echo "OK.\n";

