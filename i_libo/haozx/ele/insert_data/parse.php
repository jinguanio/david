<?php

require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'agent/em_agent_parse.class.php';

$xml_str = file_get_contents('/tmp/test.xml');
$parse = new em_agent_parse($xml_str);
//var_dump($parse->get_host());
//var_dump($parse->get_plugin());
var_dump($parse->get_postlog());
