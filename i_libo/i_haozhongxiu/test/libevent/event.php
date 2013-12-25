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
	protected $__event_buffer = null;

	/**
	 * exit_flag 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $exit_flag = array();
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
		$this->__event_base = new EventBase();
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
		$host = array('localhost:8000', 'localhost:8001');
		while(1) {
			foreach ($host as $key => $value) {
				$this->__status[$key] = self::STATUS_START;
				$fp = stream_socket_client($value, $errno, $errstr, 3);
				//stream_set_timeout($fp, 1);
				$this->__event_buffer[$key] = new EventBufferEvent($this->__event_base, $fp, EventBufferEvent::READING,
						array($this, 'ev_read'), array($this, 'ev_write'), array($this, 'ev_error'), $key);
				$this->__event_buffer[$key]->enable(Event::READ);
			}
				var_dump($this->__event_buffer);
				$this->__event_base->dispatch();
				$this->__event_base->loop();	
		}
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
		var_dump($buffer);
		$buffer->read($read, 1024);
		echo $read;
		if ($read && 0 === strpos(trim($read), '+OK')) {
			switch ($this->__status[$key]) {
				case self::STATUS_START:
					$this->__status[$key] = self::STATUS_USER;
					$buffer->enable(Event::WRITE);
					break;
				case self::STATUS_USER_RE:
					$this->__status[$key] = self::STATUS_PASS;
					$buffer->enable(Event::WRITE);
					break;			
				case self::STATUS_PASS_RE:
					$this->__status[$key] = self::STATUS_QUIT;
					$buffer->enable(Event::WRITE);
					break;			
				case self::STATUS_QUIT_RE:
					//$buffer->disable(Event::READ | Event::WRITE);
					echo "debug1111";
					$this->__exit_flag[] = $key;
					unset($this->__event_buffer[$key]);
					if (count($this->__exit_flag) == 2) {
						$this->__event_base->exit();	
						echo "dedede2323232";
					}
					break;			
			}	
		}
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
				$write = $buffer->write("user admin@test.eyou.net");
				if ($write) {
					$this->__status[$key] = self::STATUS_USER_RE;	
				}	
				break;
			case self::STATUS_PASS:
				$write = $buffer->write("pass eyouadmin");
				if ($write) {
					$this->__status[$key] = self::STATUS_PASS_RE;	
				}	
				break;
			case self::STATUS_QUIT:
				$write = $buffer->write("quit");
				if ($write) {
					$this->__status[$key] = self::STATUS_QUIT_RE;	
				}	
				break;
		}

		$buffer->enable(Event::READ);
	}

	// }}}
	// {{{ public function ev_error()

	/**
	 * 读回调 
	 * 
	 * @access public
	 * @return void
	 */
	public function ev_error()
	{
	//	$read = event_buffer_read($buffer, 1024);
	}

	// }}}
	// }}}	
}

$test = new test_event();
$test->run();	
