<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

$db = em_db::singleton();

    $select = "select * from user where email='libo@eyou.net'";
    $select = "show processlist";
    $stmt = $db->query($select);
    //$db->check_connection();
    $res = $stmt->fetch_all();
    //print_r($stmt);
    //print_r($db->get_connection());
    print_r($res);
    //print_r($stmt->getAttribute(PDO::ATTR_DRIVER_NAME));

