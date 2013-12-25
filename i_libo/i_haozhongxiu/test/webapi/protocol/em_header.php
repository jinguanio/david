<?php

class em_header
{
	// {{{ members

	/**
	 * 所有支持的 header 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__allow_headers = array(
		'User-agent' => true,
		'Accept'     => true,
		'Moudle'     => true,
		'Operator'   => true,
		'Function'   => true,
	);

	/**
	 * 协议头默认信息 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__header_default = array(
		'User-agent' => 'php',
		'Accept'     => array('text', 'xml', 'json'),
		'Moudle'     => 'index',
		'Operator'   => 'index',
		'Function'   => 'index',
	);

	/**
	 * __header 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__header = array();

	/**
	 * 客户端类型 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $__agent = array('php', 'nodejs');

	/**
	 * 可以接收的类型 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $__accept = array('text', 'xml', 'json');

	// }}}	
	// {{{ functions
	// {{{ public function set_header()
	
	/**
	 * set_header 
	 * 
	 * @access public
	 * @return void
	 */
	public function set_header($header, $data)
	{		
		$encode_header = strtolower(str_replace('-', '_', $header));
		$func = '_set_header_' . $encode_header;

		if (!array_key_exists($header, $this->__allow_headers)) {
			return false;	
		}

		$default = $this->__header_default[$header];
		if (method_exists($this, $func)) {
			$value = $this->$func($data, $default);
			$this->__header[$encode_header] = $value;
		} else {
			$this->__header[$encode_header] = $data;
		}
		
		return true;
	}

	// }}}
	// {{{ public function set_headers()
	
	/**
	 * set_headers 
	 * 
	 * @access public
	 * @return void
	 */
	public function set_headers($headers = array())
	{
		foreach ($this->__allow_headers as $header => $is) {
			$data = !isset($headers[$header]) ? $this->__header_default[$header] : $headers[$header];

			$this->set_header($header, $data);
		}	
	}

	// }}}
	// {{{ public function get_header()

	/**
	 * 获取头信息 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_header($header)
	{
		if (!array_key_exists($header, $this->__allow_headers)) {
			return null;	
		}

		if (!isset($this->__header[$header])) {
			return null;	
		}

		return $this->__header[$header];
	}

	// }}}
	// {{{ public function get_headers()

	/**
	 * 获取所有的头信息 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_headers()
	{
		if (empty($this->__header)) {
			$this->set_headers();	
		}	

		return $this->__header;
	}

	// }}}
	// {{{ protected function _set_header_accept()

	/**
	 * 设置客户端可以接收的类型
	 * 
	 * @access protected
	 * @return array
	 */
	protected function _set_header_accept($data, $default)
	{
		if (!is_array($data)) {
			if (false !== strpos($data, '/')) {
				$data = explode('/', trim($data));
			}else {
				$data = array($data);	
			}
		}

		$accept = array();
		foreach ($data as $value) {
			if (!in_array($value, $this->__accept)) {
				continue;	
			}
			
			$accept[] = $value;
		}

		return empty($accept) ? $default : $accept;
	}

	// }}}
	// }}}
}
