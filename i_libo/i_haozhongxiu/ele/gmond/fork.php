<?php

declare(ticks = 1);
//信号处理函数

class test
{
	protected $__pids = array();

	public function my_fork($process_name)
	{
		$pid = pcntl_fork();
		if ($pid == -1) {
			die('could not fork');
		} else if ($pid) {
			pcntl_signal(SIGCHLD, array($this, 'sig_handler'), true);
			$this->__pids[$pid] = $process_name;
		} else {
			while(1) {
			pcntl_signal(SIGALRM, array($this, 'handler_timeout'), false);
			pcntl_alarm(2);
				sleep(1);
				$arr = array();
				$this->$process_name($arr);	
				echo "\ndebug1111\n";
			}
			exit(1);
		}

		return $pid;
	}


	public function process_1($arr)
	{
		echo "process_1\n";
		sleep(3);
	}

	public function process_2($arr)
	{
		echo "process_2\n";
		sleep(3);
	}

	public function sig_handler($sig)
	{
		var_dump($this->__pids);
		while(($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
			//break;
			echo "debug child" . $pid . "\n";	
			if (isset($this->__pids[$pid])) {
			//	var_dump($this->my_fork($this->__pids[$pid]));
				unset($this->__pids[$pid]);	
			}
		}
	}

	public function handler_timeout($sig)
	{
		$pid = posix_getpid();
		echo $pid . "timeout";
	//	var_dump(posix_kill($pid, SIGTERM));
	}

	public function run()
	{
		$pids = array_flip($this->__pids);

		if (!isset($pids['process_1'])) {
			$pid = $this->my_fork('process_1');
		}

		if (!isset($pids['process_2'])) {
			$pid = $this->my_fork('process_2');
		}
	}
}


$test = new test();
while(1) {
	$test->run();
	sleep(1);	
};
