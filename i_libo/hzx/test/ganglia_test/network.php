<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'ganglia/em_ganglia_module_adapter_network.class.php';
class test extends em_ganglia_module_adapter_network
{
	public function test1()
	{
		return $this->_get_cache_array();	
	}
	public function test2()
	{
		return $this->_get_eths();	
	}
}

$a = new test();
print_r($a->test2());
print_r($a->test1());
