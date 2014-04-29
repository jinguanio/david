<?php
require_once EMBASE_PATH_EYOU_MAIL_CONF . 'conf_global.php';

require_once PATH_EYOUM_LIB . 'iterator/em_iterator_archive_log.class.php';

$files = new em_iterator_archive_log();

$files = array_unique(iterator_to_array($files));
var_dump($files);
//foreach ($iterator as $value)
//{
//	var_dump($value);
//}
