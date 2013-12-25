#!/usr/local/eyou/devmail/opt/bin/php
<?php
exit('stop');
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * 更新源码注释风格
 * 修改文件头注释
 * 修改类文件注释
 * 
 * @category   Tools
 * @package    Tools_style
 * @copyright  2006 Beijing eYou Information Technology Co., Ltd.
 * @version    $Id:$
 */

/**
 * require
 */
require_once 'functions.php';

/**
 * 参数配置 
 */
//MODE为true表示实际工作环境
//MODE为false表示代码测试环境
define('MODE', true);

//测试目录
//define('TESTDIR', '/usr/local/eyou/mail/app/lib/php/em_config.class.php');
//define('TESTDIR', '/usr/local/eyou/mail/app/lib/php/em_db.class.php');
define('TESTDIR', '/home/libo/crane/trunk/src/lib/member/property/test/test_em_member_property_domain_basic.class.php');

//工作目录
//define('WORKDIR', '/usr/local/eyou/mail/app/lib/php/');
//define('WORKDIR', '/home/libo/crane/trunk/src/lib/');
define('WORKDIR', '/home/libo/crane/trunk/src/lib/member/property/test/');

//需要获取的标签
$get_flag = array('package', 'subpackage');

/**
 * 功能处理 
 */
if (true === MODE) {
    $file_array = rglob('*.php', 0, WORKDIR);
} else {
    $file_array = array(TESTDIR);
}
//var_export($file_array);exit;

/**
 * preg_replace_callback回调函数 
 * 
 * @param array $matches 匹配结果
 * @return string
 */
function replace_fun($matches)
{
    //var_dump($matches);
    global $class_desc, $file_desc, $package, $subpackage;

    if (strpos($matches[0], '@version')) { //文件头说明部分
        $replace = "/**\n $file_desc\n * @category   eYou_Mail\n * @package    $package\n * @copyright  2006 Beijing eYou Information Technology Co., Ltd.\n * @version    \$Id:\$\n */";
        return $replace;
    } elseif (strpos($matches[0], '@package')) { //类头说明部分
        $sub_pack = (!empty($subpackage)) ? " * @subpackage $subpackage\n" : '';
        $replace = "/**\n $class_desc\n * @category   eYou_Mail\n * @package    $package\n" . $sub_pack . " */";
        return $replace;
    } else {
        return $matches[0];
    }
}

//处理类文件
foreach ($file_array as $file) {
    if (is_file($file)) {
        $code        = '';
        $package     = '';
        $file_desc   = '';
        $subpackage  = '';
        $flag_info   = array();
        $resource    = file_get_contents($file);

        //获取指定标签值
        foreach ($get_flag as $flag) {
            //获取@package信息
            $match = array();
            preg_match('/@'. $flag . '\s*(\w+)/i', $resource, $match);
            if (!empty($match)) {
                $flag_info[$flag] = $match[1];
            } else {
                $flag_info[$flag] = '';
            }
        }
        extract($flag_info);

        //获得文件部分注释信息
        $match = array();
        preg_match_all('/^\/\*\*.*\*\//imsU', $resource, $match);
        //var_dump($match);

        //获取文件头注释
        //包括主文件注释、require注释、类文件注释
        if (!empty($match)) {
            //文件注释
            $file_annotation = $match[0][0];
            //类注释
            //对于没有require部分的程序，$match[0][1]为class_annotation。
            $class_annotation = (!isset($match[0][2])) ? $match[0][1] : $match[0][2];
        } else {
            echo "Line: " . __LINE__ . " error. File: $file.\n";exit(1);
        }
        
        //文件和类的说明信息
        //将获得如下变量：
        //$file_desc 表示文件说明信息
        //$class_desc 表示类说明信息
        $patt_array = array('file', 'class');
        foreach ($patt_array as $patt) {
            $match = array();
            $annotation = $patt . '_annotation';
            $desc       = $patt . '_desc';

            $libo = preg_replace('/^\s*\*\s*@.*$|\/\*\*|\*\//ismU', '', $$annotation);
            $libo = trim($libo, "\n ");
            if (!empty($libo)) {
                $$desc = $libo;
            } else {
                $$desc = '*';
                //echo "Line: " . __LINE__ . " error. File: $file.\n";exit(1);
            }
        }
        //var_dump($file_desc);exit;

        //处理文件注释信息
        $code = preg_replace_callback('/\/\*\*.*?\*\//is', 'replace_fun', $resource, 3);
        //var_dump($code);

        //处理类头注释信息
        file_put_contents($file, $code);
        echo "OK. Modify $file success.\n";
    }
}

