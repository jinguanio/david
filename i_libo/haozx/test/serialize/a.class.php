<?php 
class a
{
	protected $__a0 = 10;	
	protected $__a1 = 20;	
	protected $__a2 = 30;	
	protected $__a3 = 40;	
	protected $__a4 = 50;	
	
	protected function get_a0()
	{
		return '333';	
	}

	public function __sleep()
	{
		return array('__a0');	
	}

	public function __wakeup()
	{
		echo "debug";	
	}
}


$test = new a();

$str =  serialize($test);

$newtest = unserialize($str);
