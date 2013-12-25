<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';

$module_name = 'process';
$class_name = 'em_ganglia_module_adapter_' . $module_name;
$file_path = PATH_EYOUM_LIB . 'pgmond/ganglia/' . $class_name . '.class.php';
if (!is_file($file_path)) {
	$log = "module:$module_name, class_name: $class_name is not exists.";
	em_ld::log($log, __FILE__, __LINE__, LOG_WARNING, self::LM, self::LC);
	require_once PATH_EYOUM_LIB . 'pgmond/em_pgmond_exception.class.php';
	throw new em_pgmond_exception($log); 
}

$param_file = PATH_EYOUM_DYNAMIC_GPM . 'gpm_params_' . $module_name . '.php';
if (!is_file($param_file)) {
	$log = "module:$module_name, params file: $param_file is not exists.";
	em_ld::log($log, __FILE__, __LINE__, LOG_WARNING, self::LM, self::LC);
	require_once PATH_EYOUM_LIB . 'pgmond/em_pgmond_exception.class.php';
	throw new em_pgmond_exception($log);
}

require_once $file_path;
include $param_file;
$metric = new $class_name();
$params = 'gpm_params_' . $module_name;
$metrics = $metric->metric_init($$params);
foreach ($metrics as $metric_info) {
	$data = $metric->metric_callback($metric_info['name']);
	var_dump($data);	
}
