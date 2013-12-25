#!/usr/local/eyou/mail/opt/bin/php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'os/db/em_os_db_doc.class.php';
require_once PATH_EYOUM_LIB . 'os/app/em_os_app.class.php';
require_once PATH_EYOUM_LIB . 'em_transaction.class.php';
require_once PATH_EYOUM_LIB . 'em_db.class.php';

// {{{ class em_utils_em_os_db_doc
class em_utils_em_os_db_doc extends em_os_db_doc {
    public function get_cache() {
        return $this->__cache;
    }
}
// }}}
// {{{ class em_utils_output_simple
class em_utils_output_simple {
    protected $__line_length = 70;
    protected $__char = '.';
    protected $__n = 0;

    public function __construct($length = 70, $char = '.') {
        $this->__line_length = $length;
        $this->__char = $char;
    }

    public function step() {
        echo $this->__char;
        $this->__n++;
        if ($this->__n >= $this->__line_length) {
            $this->__n = 0;
            echo "\n";
        }
    }

    public function end() {
        echo "\n";
    }
}

// }}}
// {{{ function help_add_app()
function help_add_app() {
    global $script;

    echo <<<HELP

例：
    添加app，从标准输入获取数据，JSON格式
    $script -m add-app < STDIN

    添加app，从文件获取数据，JSON格式，一行一个
    $script -m add-app -f file.json


app JSON格式:
{
    "attribute1":"xxx",
    "attribute2":"xxx",
    ...

    "win":{"win1":{},"win2":{}, ...},
    "link":{"link1":{},"link2":{}, ...},
    "action":{"action1":{},"action2":{}, ...}
}

各项允许的属性：


HELP;

    $types = array('app', 'app_win', 'app_link', 'app_action');
    foreach ($types as $type) {
        $class_name = 'em_os_property_' . $type;
        if (!class_exists($class_name)) {
            require_once PATH_EYOUM_LIB . 'os/property/' . $class_name . '.class.php';
        }
        
        $class_name_modify = 'em_utils_' . $class_name;
        if (!class_exists($class_name_modify)) {
            eval("
            class $class_name_modify extends $class_name {
                public function get_allow_attributes() {
                    return \$this->__allow_attributes;
                }
            }");
        }

        $class = new $class_name_modify;
        $allow_attributes = $class->get_allow_attributes();
        echo $type,":\n";
        echo implode(', ', array_keys($allow_attributes)), "\n\n";
    }
}
// }}}
// {{{ function help_mod_app()
function help_mod_app() {
    global $script;

    echo <<<HELP

例：
    添加app，从标准输入获取数据，JSON格式
    $script -m add-app < STDIN

app JSON格式:
{
    "attribute1":"xxx",
    "attribute2":"xxx",
    ...

    "win":{"win1":{},"win2":{}, ...},
    "link":{"link1":{},"link2":{}, ...},
    "action":{"action1":{},"action2":{}, ...}
}

各项允许的属性：


HELP;

    $types = array('app', 'app_win', 'app_link', 'app_action');
    foreach ($types as $type) {
        $class_name = 'em_os_property_' . $type;
        if (!class_exists($class_name)) {
            require_once PATH_EYOUM_LIB . 'os/property/' . $class_name . '.class.php';
        }
        
        $class_name_modify = 'em_utils_' . $class_name;
        if (!class_exists($class_name_modify)) {
            eval("
            class $class_name_modify extends $class_name {
                public function get_allow_attributes() {
                    return \$this->__allow_attributes;
                }
            }");
        }

        $class = new $class_name_modify;
        $allow_attributes = $class->get_allow_attributes();
        echo $type,":\n";
        echo implode(', ', array_keys($allow_attributes)), "\n\n";
    }
}
// }}}
// {{{ function clear_cache()
function clear_cache($app_name = null, $app_version = null) {
    $app = new em_os_app();
    $doc = new em_utils_em_os_db_doc();
    $cache = $doc->get_cache();
    $output = new em_utils_output_simple();
    
    $condition = em_condition::factory('os:app', 'app:find_app');
    if (isset($app_name)) {
        $condition->set_prefix(em_os::PREFIX_APP);
        $condition->set_eq('app', $app_name);
    }
    if (isset($app_version)) {
        $condition->set_prefix(em_os::PREFIX_APP);
        $condition->set_eq('version', $app_version);
    }
    $condition->set_is_fetch(true);

    // 删除app缓存
    $condition_app = clone $condition;
    $condition_app->set_columns(array('os_app_id'));

    $rs = $app->find_app($condition_app);
    while ($row = $rs->fetch()) {
        $cache->delete($row['os_app_id']);
        $output->step();
    }

    // 删除win缓存
    $condition_win = clone $condition;
    $condition_win->set_prefix(em_os::PREFIX_APP_WIN);
    $condition_win->set_columns(array('os_app_win_id'));

    $rs = $app->find_app($condition_win);
    while ($row = $rs->fetch()) {
        $cache->delete($row['os_app_win_id']);
        $output->step();
    }

    // 删除link缓存
    $condition_link = clone $condition;
    $condition_link->set_prefix(em_os::PREFIX_APP_LINK);
    $condition_link->set_columns(array('os_app_link_id'));

    $rs = $app->find_app($condition_link);
    while ($row = $rs->fetch()) {
        $cache->delete($row['os_app_link_id']);
        $output->step();
    }

    // 删除action缓存
    $condition_action = clone $condition;
    $condition_action->set_prefix(em_os::PREFIX_APP_ACTION);
    $condition_action->set_columns(array('os_app_action_id'));

    $rs = $app->find_app($condition_action);
    while ($row = $rs->fetch()) {
        $cache->delete($row['os_app_action_id']);
        $output->step();
    }

    $output->end();
}
// }}}
// {{{ function get_app()
function get_app($app_name = null, $app_version = null) {
    $__db = em_db::singleton();
    $__db->get_profile()->set_enabled(true); 

    $os_app = new em_os_app();

    $app_win = $os_app->get_operator('app_win');
    $app_link = $os_app->get_operator('app_link');
    $app_action = $os_app->get_operator('app_action');

    $condition = em_condition::factory('os:app', 'app:find_app');
    $condition->set_prefix(em_os::PREFIX_APP);
    $condition->set_columns(array('os_app_id'));
    $condition->set_is_fetch(true);
    if (isset($app_name)) {
        $condition->set_eq('app', $app_name);
    }
    if (isset($app_version)) {
        $condition->set_eq('version', $app_version);
    }

    $rs = $os_app->find_app($condition);
    $profile = $__db->get_profile()->get_query_profiles(null, true);
    var_dump($profile);
    $is_found = false;
    while ($row = $rs->fetch()) {
        $is_found = true;

        $app = $os_app->get_operator('app')->fetch_app_by_id($row['os_app_id']);
        foreach ($app['win'] as $win_name => $win_id) {
            $win = $app_win->fetch_app_win_by_id($row['os_app_id'], $win_id);
            $app['win'][$win_name] = $win;
        }
        foreach ($app['link'] as $link_name => $link_id) {
            $link = $app_link->fetch_app_link_by_id($row['os_app_id'], $link_id);
            $app['link'][$link_name] = $link;
        }
        foreach ($app['action'] as $action_name => $action_id) {
            $action = $app_action->fetch_app_action_by_id($row['os_app_id'], $action_id);
            $app['action'][$action_name] = $action;
        }

        //print_r($app);
        echo PHP_EOL;
    }

    if (!$is_found) {
        error_exit('Not Found.');
    }
}
// }}}
// {{{ function add_app()
function add_app($json) {
    $data = json_decode($json, true);
    if (!$data) {
        error_exit("invalid json:\n$json");
    }

    $os_app = new em_os_app();

    $trans = new em_transaction();
    $trans->begin();

    $app_property = em_os::property_factory('app', $data);
    check_property($app_property);
    $os_app->get_operator('app')->add_app($app_property);

    foreach (array('win', 'link', 'action') as $type) {
        if (!isset($data[$type])) {
            continue ;
        }
        if (!is_array($data[$type])) {
            error_exit("invalid `$type` data:\n" . $data[$type]);
        }
        foreach ($data[$type] as $name => $attributes) {
            $property = em_os::property_factory("app_$type", $attributes);
            if (!is_numeric($name)) {
                $property->{"set_$type"}($name);
            }
            $property->set_app($app_property);
            check_property($property);
            $os_app->get_operator("app_$type")->{"add_app_$type"}($property);
        }
    }

    $trans->commit();

    echo "+OK\n";
    exit(0);
}
// }}}
// {{{ function mod_app()
function mod_app($app_name, $app_version, $json) {
    $data = json_decode($json, true);
    if (!$data) {
        error_exit("invalid json:\n$json");
    }

    $os_app = new em_os_app();
    if (!isset($app_version)) {
        $condition = em_condition::factory('os:app', 'app:find_app');
        $condition->set_prefix(em_os::PREFIX_APP);
        $condition->set_columns(array('version'));
        $condition->set_eq('app', $app_name);
        $tmp = $os_app->find_app($condition);
        if (!$tmp) {
            error_exit('Not Found.');
        }
        if (1 <> count($tmp)) {
            error_exit('Found ' . count($tmp) . ' record for `' . $app_name . '`');
        }
        $app_version = $tmp[0]['version'];
    }

    $trans = new em_transaction();
    $trans->begin();

    $app_property = em_os::property_factory('app', $data);
    check_property($app_property);
    $os_app->get_operator('app')->mod_app_by_name($app_name, $app_version, $app_property);

    $app_info = $os_app->get_operator('app')->fetch_app_by_name($app_name, $app_version);
    $os_app_id = $app_info['os_app_id'];
    $app_property->set_os_app_id($os_app_id);

    foreach (array('win', 'link', 'action') as $type) {
        if (!isset($data[$type])) {
            continue ;
        }
        if (!is_array($data[$type])) {
            error_exit("invalid `$type` data:\n" . $data[$type]);
        }
        foreach ($data[$type] as $name => $attributes) {
            if (!is_numeric($name) && isset($attributes[$type])) {
                $name = $attributes[$type];
            }
            unset($attributes[$type]);
            $property = em_os::property_factory("app_$type", $attributes);
            $property->set_app($app_property);
            check_property($property);
            
            if (isset($app_info[$type][$name])) {
                $os_app->get_operator("app_$type")->{"mod_app_{$type}_by_name"}($os_app_id, $name, $property);
                unset($app_info[$type][$name]);
            } else {
                $property->{"set_$type"}($name);
                $os_app->get_operator("app_$type")->{"add_app_{$type}"}($property);
            }
        }
    }

    foreach (array('win', 'link', 'action') as $type) {
        if (empty($app_info[$type])) {
            continue ;
        }
        $os_app->get_operator("app_$type")->{"del_app_{$type}_by_id"}($os_app_id, $app_info[$type]);
    }

    $trans->commit();

    echo "+OK\n";
    exit(0);
}
// }}}
// {{{ function del_app()
function del_app($app_name = null, $app_version = null) {
    $os_app = new em_os_app();

    $condition = em_condition::factory('os:app', 'app:find_app');
    $condition->set_prefix(em_os::PREFIX_APP);
    $condition->set_columns(array('os_app_id', 'app', 'version'));
    $condition->set_is_fetch(true);
    if (isset($app_name)) {
        $condition->set_eq('app', $app_name);
    }
    if (isset($app_version)) {
        $condition->set_eq('version', $app_version);
    }

    $rs = $os_app->find_app($condition);
    $ids = array();
    while ($row = $rs->fetch()) {
        $ids[] = $row['os_app_id'];
        vprintf("os_app_id:%s, app:%s, version:%s\n", $row);
    }
    
    if (!$ids) {
        error_exit('Not Found');
    }

    do {
        echo "\nDelete? [y/n] ";
        $yes = fgets(STDIN);
        if ($yes) {
            $yes = trim($yes);
        }
        switch ($yes) {
            case 'y':
            case 'Y':
                $yes = true;
                break;

            case 'n':
            case 'N':
                $yes = false;
                break;

            case false:
                $yes = false;
                break;

            default:
                $yes = null;
                break;
        }
    } while (!isset($yes));

    if (!$yes) {
        echo "Aborted.\n";
        exit;
    }

    $os_app->get_operator('app')->del_app_by_id($ids);
    echo "+OK\n";
    exit(0);
}
// }}}
// {{{ function check_property()
function check_property($property) {
    try {
        $property->check();
        return ;
    } catch (exception $e) {
    }

    $validate = $property->get_validate();
    if (!$validate) {
        throw $e;
    }

    echo '-ERR, ', $e->getMessage(),"\n";
    $attributes = $property->attributes();
    foreach ($validate as $item) {
        printf("[%s:%s] %s\n", $item, $attributes[$item], $property->get_restrict($item));
    }
    exit(1);
}
// }}}
// {{{ function error_exit()
function error_exit($msg, $code = 1) {
    echo '-ERR, ', $msg, "\n";
    exit($code);
}
// }}}

// Test
get_app();

