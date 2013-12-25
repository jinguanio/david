<?php
define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'config/em_config_dynamic.class.php';
 
// 无论是调用 em_config_map 中的方法还是 em_config_dynamic 中的方法都需要首先创建 em_config_dynamic 对象
// 因为引入插件配置是通过创建此对象来实现的
$config_dynamic = new em_config_dynamic;

// 获取本地所有在 ini 中的配置, 返回 array
$res_ini = $config_dynamic->get_all_config_ini();

// 获取所有允许配置的默认值, 返回 array
$res_default = $config_dynamic->get_all_config_default();

// 获取所有允许配置的值, 如果已经配置则返回已经配置的值, 如果没有配置则返回默认值, 返回 array
$res_all = $config_dynamic->get_all_config();

// 获取系统所有允许的 config name, 返回 array
$res_all_name = em_config_map::get_all_config_name();

// 获取类型, 分类 以及 是否是多值类型
$config_info = array();
foreach ($res_all_name as $config_name) {
    /**
     * 获取每个 config 的 map, map 结构如下
     * array(
     *     'default' => '',   // 默认值, 此元素一定存在
     *     'type'    => 1,    // 类型, 此元素一定存在, 可能是 em_config::TYPE_STR | em_config::TYPE_ARRAY
     *     'multi'   => true, // 是否为多值的类型 (也就是 # 规则), 此元素可能不存在, 如果不存在则当作 false
     *     'cat'     => 'db', // 分类, 此元素可能不存在, 如果不存在则认为是 其他 类别
     * )
     */
    $config_map = em_config_map::get_one_map($config_name);
    $config_info[$config_name] = $config_map;
    //var_export($config_map);
    //echo "\n";
}
    //$config_map = em_config_map::get_one_map('group_alias_postfix');
    //var_export($config_map);
    //exit(0);

// 获取分类和每个参数的配置信息
$_cate = array();
foreach ($config_info as $config_name => $desc_map) {
    if (isset($desc_map['cat'])) {
        $_cate[$desc_map['cat']][$config_name] = $desc_map;
    } else {
        $_cate['other'][$config_name] = $desc_map;
    }
}
//print_r($_cate);

// 获取分类和配置名
$_cate_name = array();
foreach ($_cate as $cate => $conf) {
    foreach ($conf as $cn => $desc) {
        $_cate_name[$cate][] = $cn;
    }
}
var_export($_cate_name);

