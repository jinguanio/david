<?php
class parse_xml
{
	// {{{ members

	protected $__xml = null;

	// }}}
	// {{{ functions
	// {{{ public function run()
	
	/**
	 * run 
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		$this->_parse_cluster();	
	}

	// }}}
	// {{{ protected function  __construct()

	/**
	 * __construct 
	 * 
	 * @access protected
	 * @return void
	 */
	public function  __construct()
	{
		$fp = stream_socket_client('172.16.100.114:8649', $errno, $errstr, 3);
		$str = '';
		while(!feof($fp)) {
			$str .= stream_socket_recvfrom($fp, 4096);
		}
		$this->__xml = new SimpleXMLElement($str, 0, false);
	}

	// }}}
	// {{{ protected function  _parse_cluster()

	/**
	 * 解析 CLUSTER 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function  _parse_cluster()
	{
		foreach ($this->__xml as $value) {
			$attr = array_values((array)$value->attributes());
			$this->_parse_host($value);
			echo json_encode($attr[0]);
			echo "\n=====\n";	
		}
	}

	// }}}
	// {{{ protected function  _parse_host()

	/**
	 * 解析 HOST 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function  _parse_host($host)
	{
		foreach ($host as $value) {
			$attr = array_values((array)$value->attributes());
			$this->_parse_metric($value);
			//var_dump($value);
			echo json_encode($attr[0]);
			echo "\n=====\n";	
		}
	}

	// }}}
	// {{{ protected function  _parse_metric()

	/**
	 * 解析 METRIC 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function  _parse_metric($metric)
	{
		foreach ($metric as $value) {
			$attr = array_values((array)$value->attributes());
			//var_dump($value);
			foreach ($value as $extra) {
				foreach ($extra as $val) {
					$extra = array_values((array)$val->attributes());
					if ('GROUP' === $extra[0]['NAME']) {
						$group = $extra[0]['VAL'];	
					}
				}	
			}
			$attr[0]['GROUP'] = $group;
			echo json_encode($attr[0]);
			echo "\n=====\n";	
		}
	}

	// }}}
	// }}}	
}

$test = new parse_xml();

$test->run();

