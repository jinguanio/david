<?php
class em_request
{
	// {{{ members

	/**
	 * __header 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $__header = null;

	// }}}
	// {{{ functions
	// {{{ public function __construct()

	/**
	 * __construct 
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		require_once 'em_header.php';
		$this->__header = new em_header();	
	}

	// }}}
	// {{{ public function set_request()

	/**
	 * set_request 
	 * 
	 * @access public
	 * @return void
	 */
	public function set_request($content)
	{
		$headers = array();
		$swan_headers = explode(PHP_EOL, ltrim($content));
		foreach ($swan_headers as $key => $value) {
			if ('' === $value) {
				break;	
			}
			$headers[] = $value;	
			unset($swan_headers[$key]);
		}

		$this->_set_request_header($headers);
	}

	// }}}	
	// {{{ public function get_header()

	/**
	 * 获取 header 
	 * 
	 * @param string $header 
	 * @param string $default 
	 * @access public
	 * @return void
	 */
	public function get_header($header, $default = null)
	{
		$data = $this->__header->get_header($header);
		if (!isset($data)) {
			$data = $default;	
		}

		return $data;
	}

	// }}}
	// {{{ public function get_headers()

	/**
	 * 获取 headers 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_headers()
	{
		$data = $this->__header->get_headers();

		return $data;
	}

	// }}}
	// {{{ protected function _set_request_header()

	/**
	 * 设置头信息
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _set_request_header($headers)
	{
		foreach ($headers as $string) {
			if (false === strpos($string, ':')) {
				continue;	
			}	

			$data = explode(':', $string, 2);

			if (!isset($data[0]) || !isset($data[1])) {
				continue;	
			}

			$this->__header->set_header($data[0], $data[1]);
		}
	}

	// }}}
	// }}}
}

$request = <<<EOD
User-agent:php/java/nodejs
Accept:json/text/xml
Moudle:member
Operator:host
Function:get_hostname
Param:333&ddsdsd&true

name=323232&age=12
EOD;
$test = new em_request();
$test->set_request($request);
$a = $test->get_headers();
var_dump($a);
