<?php
error_reporting(E_ALL);

declare(ticks = 1);

/**
 * Daemon 
 * 
 * @author David
 *
 * Example:
 *   $daemon = new Daemon;
 *   $daemon->set_options(
 *      [
 *          'func' => 'get_queue',
 *          'fork_num' => 10,
 *      ]
 *   );
 *   $daemon->fork_proc();
 *
 *   $daemon->kill_proc();
 *
 */
class Daemon
{
    // {{{ members

    /**
     * 子进程 id 
     * 
     * @var mixed
     */
    private $__pids = [];

    /**
     * 子进程执行行数 
     * 
     * @var string
     */
    private $__func = '';

    /**
     * 子进程个数 
     * 
     * @var float
     */
    private $__fork_num = 5;

    /**
     * pid 文件路径 
     * 
     * @var string
     */
    private $__pid_file = '/tmp/proc.pid';

    // }}}
    // {{{ functions
    // {{{ public function __construct()

    /**
     * __construct 
     * 
     * @param array $args 
     *  [
     *      'func' => 'abc',
     *      'fork_num' => 5,
     *      'pid_file' => '/tmp/proc.pid',
     *  ]
     * @return void
     */
    public function __construct($args = null)
    {
        $this->set_options($args);
    }

    // }}}
    // {{{ public function set_options()

    /**
     * 设置配置参数 
     * 
     * @param array $args 
     *  [
     *      'func' => 'abc',
     *      'fork_num' => 5,
     *      'pid_file' => '/tmp/proc.pid',
     *  ]
     * @return void
     */
    public function set_options($args)
    {
        if (isset($args['func'])) {
            $this->__func = $args['func'];
        }

        if (isset($args['fork_num'])) {
            $this->__fork_num = $args['fork_num'];
        }

        if (isset($args['pid_file'])) {
            $this->__pid_file = $args['pid_file'];
        }
    }

    // }}}
    // {{{ public function set_option()

    /**
     * 设置配置参数 
     * 
     * @param string $name 
     * @param mixed $val 
     * @return void
     */
    public function set_option($name, $val)
    {
        $prop = '__' . $name;
        if (property_exists($this, $prop)) {
            $this->$prop = $val;
        }
    }

    // }}}
    // {{{ public function fork_proc()

    /**
     * 创建多进程 
     * 
     * @return void
     */
    public function fork_proc()
    {
        // 忽略终端 I/O信号,STOP信号
        pcntl_signal(SIGTTOU, SIG_IGN);
        pcntl_signal(SIGTTIN, SIG_IGN);
        pcntl_signal(SIGTSTP, SIG_IGN);
        pcntl_signal(SIGHUP, SIG_IGN);
       
        $pid = pcntl_fork();
        if (0 != $pid) {
            exit(0);
        }

        $this->_write_pid();
        posix_setsid();
        chdir('/tmp');
        umask(0);
        $this->_fork_child();

        pcntl_signal(SIGUSR1, array($this, '_kill_child'));

        while (1) {
            sleep(60);
        }
    }

    // }}}
    // {{{ public function kill_proc()

    /**
     * 停止进程 
     * 
     * @return void
     */
    public function kill_proc()
    {
        $ppid = file_get_contents($this->__pid_file);
        posix_kill($ppid, SIGUSR1);
    }

    // }}}
    // {{{ private function _fork_child()

    /**
     * 创建子进程 
     * 
     * @param int $num 子进程个数
     * @return void
     */
    private function _fork_child()
    {
        for ($i = 0; $i < $this->__fork_num; $i++) {
            $pid = pcntl_fork();

            pcntl_signal(SIGTERM, SIG_DFL);
            pcntl_signal(SIGCHLD, SIG_DFL);

            if ($pid == -1) {
                die('could not fork');
            } else if ($pid) {
                $this->__pids[] = $pid;
                pcntl_wait($status, WNOHANG); 
            } else {
                call_user_func($this->__func);
            }
        }
    }

    // }}}
    // {{{ private function _kill_child()

    /**
     * 停止子进程 
     * 
     * @return void
     */
    private function _kill_child()
    {
        foreach ($this->__pids as $p) {
            posix_kill($p, SIGTERM);
        }

        $ppid = file_get_contents($this->__pid_file);
        posix_kill($ppid, SIGTERM);
    }

    // }}}
    // {{{ private function _write_pid()

    /**
     * 写入 pid 文件 
     * 
     * @return void
     */
    private function _write_pid()
    {
        $fp = dio_open($this->__pid_file, O_WRONLY|O_CREAT, 0644);  
        dio_truncate($fp, 0);
        dio_write($fp, posix_getpid()); 
    }

    // }}}
    // }}}
}

