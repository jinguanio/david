<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'ganglia/em_ganglia_gmond.class.php';

$params = array(
	'name' => 'test_hzxzzzz',
	'tmax' => 60,
	'value_type' => 'float',
	'slope' => 'both',
	'value' => 60.9999,
	'units' => 'n',
);
em_ganglia_gmond::gmetric_send('127.0.0.1:8649', $params);
