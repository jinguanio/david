<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 功能类、函数包
 * 
 * @category   Tools
 * @package    Tools_base
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id:$
 */

// {{{ function rglob()

/**
 * 递归返回指定目录，指定样式文件
 * 
 * @param string $pattern 文件模板
 * @param int $flags 修改标志
 * @param string $path 路径
 * @return array 文件名路径数组
 */
function rglob($pattern = '*', $flags = 0, $path = '')
{
    $paths = glob($path . '*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path . $pattern, $flags);
    foreach ($paths as $path) { 
        $files = array_merge($files, rglob($pattern, $flags, $path)); 
    }
    return $files;
}

// }}}
