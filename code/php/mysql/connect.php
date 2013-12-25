<?php
error_reporting(E_ALL);

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

$opt = [
    'dbname' => 'test',
    'username' => 'root',
    'unix_socket' => '/usr/local/eyou/toolmail/run/etm_mysql.sock',
];
$table = 'post';
$count = 3;

$plugin_name = [
    'p1',
    'p2',
];

$db = em_db::singleton('mysql', $opt);
for ($i = 0; $i < $count; $i++) {
    $time = time() + $i*300;
    foreach ($plugin_name as $p) {
        $ct = $time + mt_rand(1, 100);
        $param = [
            'plugin_name' => $p,
            'job_time' => $ct,
            'title' => $p . '-------' . date('Y-m-d H:i:s', $ct),
        ];
        var_dump($p);
        $db->insert($table, $param);
    }
}

$query = $db->select()->from($table);
$ret = $db->fetch_all($query);
print_r($ret);

//explain select c.serial_id, c.plugin_name, c.job_time, c.title from (select plugin_name, max(job_time) as max from postlog group by plugin_name) b inner join postlog c on b.plugin_name = c.plugin_name and b.max = c.job_time;

