<?php
class test_event
{
	// {{{ const

	const STATUS_START = 0;
	const STATUS_USER  = 1;
	const STATUS_USER_RE = 2;
	const STATUS_PASS  = 3;
	const STATUS_PASS_RE = 4;
	const STATUS_QUIT  = 5;
	const STATUS_QUIT_RE = 6;

	// }}}
	// {{{ members

	/**
	 * status 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $__status = array();

	/**
	 * __event_status 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $__event_base = null;

	/**
	 * __event_status 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $__events = null;
	// }}}
	// {{{ function
	// {{{ public function __construct()
	
	/**
	 * __construct 
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->__event_base = event_base_new();
	}

	// }}}
	// {{{ public function run()

	/**
	 * run 
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		$array = array('localhost:8003');

			foreach ($array as $key => $value) {
				$fp = stream_socket_client($value, $errno, $errstr, 30);
				if (!$fp) {
					echo "host is down";	
				}
				$this->__status[$key] = self::STATUS_START;
				$buffer = event_buffer_new($fp, array($this, 'ev_read'), array($this, 'ev_write'), array($this, 'ev_error'), $key);
				event_buffer_base_set($buffer, $this->__event_base);
				event_buffer_timeout_set($buffer, 1, 1);
				event_buffer_enable($buffer, EV_READ | EV_PERSIST);
			}
			event_base_loop($this->__event_base);	
	}

	// }}}
	// {{{ public function ev_read()

	/**
	 * 读回调 
	 * 
	 * @access public
	 * @return void
	 */
	public function ev_read($buffer, $key)
	{
		$read = event_buffer_read($buffer, 1024);
		echo $read;
		if ($read && 0 === strpos(trim($read), '+OK')) {
			switch ($this->__status[$key]) {
				case self::STATUS_START:
					$this->__status[$key] = self::STATUS_USER;
					break;
				case self::STATUS_USER_RE:
					$this->__status[$key] = self::STATUS_PASS;
					break;			
				case self::STATUS_PASS_RE:
					$this->__status[$key] = self::STATUS_QUIT;
					break;			
			}	
		}
		event_buffer_enable($buffer, EV_WRITE);
	}

	// }}}
	// {{{ public function ev_write()

	/**
	 * 读回调 
	 * 
	 * @access public
	 * @return void
	 */
	public function ev_write($buffer, $key)
	{
		switch ($this->__status[$key]) {
			case self::STATUS_USER:
				$write = event_buffer_write($buffer, "user admin@test.eyou.net\r\n");
				if ($write) {
					$this->__status[$key] = self::STATUS_USER_RE;	
				}	
				break;
			case self::STATUS_PASS:
				$write = event_buffer_write($buffer, "pass eyouadmin\r\n");
				if ($write) {
					$this->__status[$key] = self::STATUS_PASS_RE;	
				}	
				break;
			case self::STATUS_QUIT:
				$write = event_buffer_write($buffer, "quit\r\n");
				if ($write) {
					$this->__status[$key] = self::STATUS_QUIT_RE;	
				}	
				break;
		}
		event_buffer_enable($buffer, EV_READ | EV_PERSIST);
	}

	// }}}
	// {{{ public function ev_error()

	/**
	 * 读回调 
	 * 
	 * @access public
	 * @return void
	 */
	public function ev_error($buffer, $flag, $key)
	{
		echo $flag;
	}

	// }}}
	// }}}	
}

$test = new test_event();
$test->run();	
