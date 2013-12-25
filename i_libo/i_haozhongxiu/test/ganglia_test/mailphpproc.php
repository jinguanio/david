<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'ganglia/em_ganglia_module_adapter_mailphpproc.class.php';
class test extends em_ganglia_module_adapter_mailphpproc
{
	public function test1()
	{
		return $this->_get_cache_array();	
	}
}

$a = new test();
print_r($a->test1());
